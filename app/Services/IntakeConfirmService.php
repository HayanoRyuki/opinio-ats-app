<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\IntakeStatus;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\IntakeCandidateDraft;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IntakeConfirmService
{
    /**
     * ドラフトを確定し、SoT（Person, Candidate, Application）を作成する
     *
     * 仮応募（is_preliminary = true）の場合は、先に promoteToFormal() を呼ぶか、
     * confirmAndPromote() を使用すること。
     */
    public function confirm(IntakeCandidateDraft $draft, User $confirmedBy): array
    {
        // 仮応募の場合は昇格が必要
        if ($draft->isPreliminary() && !$draft->isPromoted()) {
            throw new \InvalidArgumentException(
                '仮応募を確定するには、先に正式応募に昇格する必要があります。' .
                'promoteToFormal() を呼ぶか、confirmAndPromote() を使用してください。'
            );
        }

        return DB::transaction(function () use ($draft, $confirmedBy) {
            return $this->confirmInternal($draft, $confirmedBy);
        });
    }

    /**
     * 仮応募を正式応募に昇格してから確定する
     *
     * スカウト反応などの仮応募を、面談確定後に正式候補者として登録する場合に使用。
     */
    public function confirmAndPromote(IntakeCandidateDraft $draft, User $confirmedBy): array
    {
        return DB::transaction(function () use ($draft, $confirmedBy) {
            // 仮応募の場合は先に昇格
            if ($draft->isPreliminary() && !$draft->isPromoted()) {
                $draft->promoteToFormal();
                $draft->refresh();
            }

            // 確定処理を実行
            return $this->confirmInternal($draft, $confirmedBy);
        });
    }

    /**
     * 確定処理の内部実装
     */
    private function confirmInternal(IntakeCandidateDraft $draft, User $confirmedBy): array
    {
        $intake = $draft->applicationIntake;
        $companyId = $intake->company_id;

        // 1. Person を取得または作成
        $person = $this->findOrCreatePerson($draft);

        // 2. Candidate を取得または作成
        $candidate = $this->findOrCreateCandidate($person, $companyId, $intake->channel);

        // 3. Application を作成（求人が指定されている場合）
        $application = null;
        if ($intake->job_id) {
            $application = $this->createApplication($candidate, $intake->job_id);
        }

        // 4. ドラフトと取り込みのステータスを更新
        $draft->update([
            'status' => IntakeStatus::CONFIRMED,
            'matched_person_id' => $person->id,
            'matched_candidate_id' => $candidate->id,
            'confirmed_by' => $confirmedBy->id,
            'confirmed_at' => now(),
        ]);

        $intake->update([
            'status' => IntakeStatus::CONFIRMED,
        ]);

        return [
            'person' => $person,
            'candidate' => $candidate,
            'application' => $application,
        ];
    }

    /**
     * 仮応募を正式応募に昇格する（確定はしない）
     *
     * 面談確定したが、まだ選考には進めない場合に使用。
     */
    public function promote(IntakeCandidateDraft $draft): void
    {
        if (!$draft->canPromote()) {
            throw new \InvalidArgumentException('この応募は昇格できません。');
        }

        $draft->promoteToFormal();
    }

    /**
     * ドラフトを却下する
     */
    public function reject(IntakeCandidateDraft $draft): void
    {
        DB::transaction(function () use ($draft) {
            $draft->update([
                'status' => IntakeStatus::REJECTED,
            ]);

            $draft->applicationIntake->update([
                'status' => IntakeStatus::REJECTED,
            ]);
        });
    }

    /**
     * Person を検索または作成
     */
    private function findOrCreatePerson(IntakeCandidateDraft $draft): Person
    {
        // 既にマッチ済みの Person がある場合はそれを使用
        if ($draft->matched_person_id) {
            return Person::findOrFail($draft->matched_person_id);
        }

        // メールアドレスで既存の Person を検索
        if ($draft->email) {
            $existingPerson = Person::where('email', $draft->email)->first();
            if ($existingPerson) {
                return $existingPerson;
            }
        }

        // 電話番号で既存の Person を検索
        if ($draft->phone) {
            $existingPerson = Person::where('phone', $draft->phone)->first();
            if ($existingPerson) {
                return $existingPerson;
            }
        }

        // 新規作成
        return Person::create([
            'name' => $draft->name,
            'email' => $draft->email,
            'phone' => $draft->phone,
            'profile' => $draft->extracted_data,
        ]);
    }

    /**
     * Candidate を検索または作成
     */
    private function findOrCreateCandidate(Person $person, int $companyId, mixed $channel): Candidate
    {
        // 既にこの企業の Candidate として登録されているか確認
        $existingCandidate = Candidate::where('company_id', $companyId)
            ->where('person_id', $person->id)
            ->first();

        if ($existingCandidate) {
            return $existingCandidate;
        }

        // 新規作成
        return Candidate::create([
            'company_id' => $companyId,
            'person_id' => $person->id,
            'source_channel' => $channel,
        ]);
    }

    /**
     * Application を作成
     */
    private function createApplication(Candidate $candidate, int $jobId): Application
    {
        // 同じ求人への応募が既にあるか確認
        $existingApplication = Application::where('candidate_id', $candidate->id)
            ->where('job_id', $jobId)
            ->first();

        if ($existingApplication) {
            return $existingApplication;
        }

        return Application::create([
            'candidate_id' => $candidate->id,
            'job_id' => $jobId,
            'status' => ApplicationStatus::ACTIVE,
            'applied_at' => now(),
        ]);
    }

    /**
     * 重複候補を検索
     */
    public function findDuplicates(IntakeCandidateDraft $draft, int $companyId): array
    {
        $duplicates = [
            'persons' => [],
            'candidates' => [],
        ];

        // メールアドレスで検索
        if ($draft->email) {
            $personByEmail = Person::where('email', $draft->email)->first();
            if ($personByEmail) {
                $duplicates['persons'][] = [
                    'person' => $personByEmail,
                    'match_type' => 'email',
                ];

                // この企業の Candidate も検索
                $candidateByEmail = Candidate::where('company_id', $companyId)
                    ->where('person_id', $personByEmail->id)
                    ->first();
                if ($candidateByEmail) {
                    $duplicates['candidates'][] = [
                        'candidate' => $candidateByEmail,
                        'match_type' => 'email',
                    ];
                }
            }
        }

        // 電話番号で検索
        if ($draft->phone) {
            $personByPhone = Person::where('phone', $draft->phone)->first();
            if ($personByPhone && ! collect($duplicates['persons'])->pluck('person.id')->contains($personByPhone->id)) {
                $duplicates['persons'][] = [
                    'person' => $personByPhone,
                    'match_type' => 'phone',
                ];

                $candidateByPhone = Candidate::where('company_id', $companyId)
                    ->where('person_id', $personByPhone->id)
                    ->first();
                if ($candidateByPhone) {
                    $duplicates['candidates'][] = [
                        'candidate' => $candidateByPhone,
                        'match_type' => 'phone',
                    ];
                }
            }
        }

        return $duplicates;
    }
}
