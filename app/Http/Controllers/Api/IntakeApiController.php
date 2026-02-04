<?php

namespace App\Http\Controllers\Api;

use App\Enums\IntakeChannel;
use App\Enums\IntakeStatus;
use App\Http\Controllers\Controller;
use App\Models\ApplicationIntake;
use App\Models\IntakeCandidateDraft;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IntakeApiController extends Controller
{
    /**
     * Web応募API - 自社採用サイトからの応募受付
     *
     * POST /api/intake/web
     */
    public function web(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_id' => ['required', 'integer', 'exists:recruitment_jobs,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'resume_url' => ['nullable', 'url'],
            'cover_letter' => ['nullable', 'string'],
            'profile' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // 求人が指定された企業のものか確認
        $job = Job::where('id', $data['job_id'])
            ->where('company_id', $data['company_id'])
            ->where('status', 'open')
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'error' => '指定された求人が見つからないか、募集が終了しています。',
            ], 404);
        }

        try {
            $result = DB::transaction(function () use ($data, $job) {
                // ApplicationIntake 作成
                $intake = ApplicationIntake::create([
                    'company_id' => $data['company_id'],
                    'job_id' => $job->id,
                    'channel' => IntakeChannel::DIRECT,
                    'source_id' => 'web_' . now()->timestamp . '_' . uniqid(),
                    'raw_data' => $data,
                    'status' => IntakeStatus::RECEIVED,
                    'received_at' => now(),
                ]);

                // IntakeCandidateDraft 作成
                $draft = IntakeCandidateDraft::create([
                    'application_intake_id' => $intake->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'extracted_data' => array_merge(
                        $data['profile'] ?? [],
                        [
                            'resume_url' => $data['resume_url'] ?? null,
                            'cover_letter' => $data['cover_letter'] ?? null,
                        ]
                    ),
                ]);

                // ステータスを処理中に更新
                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'draft_id' => $draft->id,
                ];
            });

            Log::info('Web応募を受け付けました', $result);

            return response()->json([
                'success' => true,
                'message' => '応募を受け付けました。',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Web応募の処理に失敗しました', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'error' => '応募の処理中にエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * エージェントAPI - 人材紹介会社からの推薦受付
     *
     * POST /api/intake/agent
     *
     * エージェント経由は書類選考が前提のため、履歴書（resume_url）は必須。
     */
    public function agent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_id' => ['required', 'integer', 'exists:recruitment_jobs,id'],
            'agent_company' => ['required', 'string', 'max:255'],
            'agent_name' => ['nullable', 'string', 'max:255'],
            'agent_email' => ['nullable', 'email', 'max:255'],
            'candidate' => ['required', 'array'],
            'candidate.name' => ['required', 'string', 'max:255'],
            'candidate.email' => ['nullable', 'email', 'max:255'],
            'candidate.phone' => ['nullable', 'string', 'max:50'],
            'candidate.resume_url' => ['required', 'url'], // エージェント経由は履歴書必須
            'candidate.recommendation' => ['nullable', 'string'],
            'candidate.profile' => ['nullable', 'array'],
        ], [
            'candidate.resume_url.required' => 'エージェント経由の推薦には履歴書（resume_url）が必須です。',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // 求人確認
        $job = Job::where('id', $data['job_id'])
            ->where('company_id', $data['company_id'])
            ->where('status', 'open')
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'error' => '指定された求人が見つからないか、募集が終了しています。',
            ], 404);
        }

        try {
            $result = DB::transaction(function () use ($data, $job) {
                $intake = ApplicationIntake::create([
                    'company_id' => $data['company_id'],
                    'job_id' => $job->id,
                    'channel' => IntakeChannel::AGENT,
                    'source_id' => 'agent_' . now()->timestamp . '_' . uniqid(),
                    'raw_data' => $data,
                    'status' => IntakeStatus::RECEIVED,
                    'received_at' => now(),
                ]);

                $candidate = $data['candidate'];
                $draft = IntakeCandidateDraft::create([
                    'application_intake_id' => $intake->id,
                    'name' => $candidate['name'],
                    'email' => $candidate['email'] ?? null,
                    'phone' => $candidate['phone'] ?? null,
                    'extracted_data' => array_merge(
                        $candidate['profile'] ?? [],
                        [
                            'resume_url' => $candidate['resume_url'],
                            'recommendation' => $candidate['recommendation'] ?? null,
                            'agent_company' => $data['agent_company'],
                            'agent_name' => $data['agent_name'] ?? null,
                            'agent_email' => $data['agent_email'] ?? null,
                        ]
                    ),
                ]);

                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'draft_id' => $draft->id,
                ];
            });

            Log::info('エージェント推薦を受け付けました', $result);

            return response()->json([
                'success' => true,
                'message' => '推薦を受け付けました。',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            Log::error('エージェント推薦の処理に失敗しました', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'error' => '推薦の処理中にエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * スカウトAPI - スカウトサービスからの反応受付
     *
     * POST /api/intake/scout
     *
     * スカウト反応は「興味あり」の段階であり、正式な応募ではない。
     * ATS上では「仮応募（is_preliminary = true）」として扱い、
     * 面談確定などで正式応募に昇格する。
     */
    public function scout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_id' => ['nullable', 'integer', 'exists:recruitment_jobs,id'],
            'scout_service' => ['required', 'string', 'max:255'], // ビズリーチ、Wantedly、Green等
            'scout_id' => ['nullable', 'string', 'max:255'],
            'response_type' => ['nullable', 'string', 'in:interested,want_to_talk,applied'], // 反応の種類
            'candidate' => ['required', 'array'],
            'candidate.name' => ['required', 'string', 'max:255'],
            'candidate.email' => ['nullable', 'email', 'max:255'],
            'candidate.phone' => ['nullable', 'string', 'max:50'],
            'candidate.profile_url' => ['nullable', 'url'],
            'candidate.profile' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // job_idが指定されている場合は確認
        $job = null;
        if (!empty($data['job_id'])) {
            $job = Job::where('id', $data['job_id'])
                ->where('company_id', $data['company_id'])
                ->where('status', 'open')
                ->first();

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'error' => '指定された求人が見つからないか、募集が終了しています。',
                ], 404);
            }
        }

        try {
            $result = DB::transaction(function () use ($data, $job) {
                // スカウトは仮応募として扱う（is_preliminary = true）
                $intake = ApplicationIntake::create([
                    'company_id' => $data['company_id'],
                    'job_id' => $job?->id,
                    'channel' => IntakeChannel::SCOUT,
                    'source_id' => 'scout_' . ($data['scout_id'] ?? now()->timestamp . '_' . uniqid()),
                    'raw_data' => $data,
                    'status' => IntakeStatus::RECEIVED,
                    'is_preliminary' => true, // 仮応募フラグ
                    'received_at' => now(),
                ]);

                $candidate = $data['candidate'];
                $draft = IntakeCandidateDraft::create([
                    'application_intake_id' => $intake->id,
                    'name' => $candidate['name'],
                    'email' => $candidate['email'] ?? null,
                    'phone' => $candidate['phone'] ?? null,
                    'is_preliminary' => true, // 仮応募フラグ
                    'extracted_data' => array_merge(
                        $candidate['profile'] ?? [],
                        [
                            'profile_url' => $candidate['profile_url'] ?? null,
                            'scout_service' => $data['scout_service'],
                            'scout_id' => $data['scout_id'] ?? null,
                            'response_type' => $data['response_type'] ?? 'interested',
                        ]
                    ),
                ]);

                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'draft_id' => $draft->id,
                    'is_preliminary' => true,
                ];
            });

            Log::info('スカウト反応を受け付けました（仮応募）', $result);

            return response()->json([
                'success' => true,
                'message' => 'スカウト反応を受け付けました。面談確定後に正式応募へ昇格できます。',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            Log::error('スカウト反応の処理に失敗しました', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'error' => '応募の処理中にエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * リファラルAPI - 社員紹介
     *
     * POST /api/intake/referral
     */
    public function referral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_id' => ['nullable', 'integer', 'exists:recruitment_jobs,id'],
            'referrer' => ['required', 'array'],
            'referrer.employee_id' => ['nullable', 'string', 'max:255'],
            'referrer.name' => ['required', 'string', 'max:255'],
            'referrer.email' => ['required', 'email', 'max:255'],
            'referrer.department' => ['nullable', 'string', 'max:255'],
            'referrer.relationship' => ['nullable', 'string', 'max:255'], // 友人、元同僚等
            'candidate' => ['required', 'array'],
            'candidate.name' => ['required', 'string', 'max:255'],
            'candidate.email' => ['nullable', 'email', 'max:255'],
            'candidate.phone' => ['nullable', 'string', 'max:50'],
            'candidate.recommendation' => ['nullable', 'string'],
            'candidate.profile' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // job_idが指定されている場合は確認
        $job = null;
        if (!empty($data['job_id'])) {
            $job = Job::where('id', $data['job_id'])
                ->where('company_id', $data['company_id'])
                ->where('status', 'open')
                ->first();

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'error' => '指定された求人が見つからないか、募集が終了しています。',
                ], 404);
            }
        }

        try {
            $result = DB::transaction(function () use ($data, $job) {
                $intake = ApplicationIntake::create([
                    'company_id' => $data['company_id'],
                    'job_id' => $job?->id,
                    'channel' => IntakeChannel::REFERRAL,
                    'source_id' => 'referral_' . now()->timestamp . '_' . uniqid(),
                    'raw_data' => $data,
                    'status' => IntakeStatus::RECEIVED,
                    'received_at' => now(),
                ]);

                $candidate = $data['candidate'];
                $referrer = $data['referrer'];

                $draft = IntakeCandidateDraft::create([
                    'application_intake_id' => $intake->id,
                    'name' => $candidate['name'],
                    'email' => $candidate['email'] ?? null,
                    'phone' => $candidate['phone'] ?? null,
                    'extracted_data' => array_merge(
                        $candidate['profile'] ?? [],
                        [
                            'recommendation' => $candidate['recommendation'] ?? null,
                            'referrer_employee_id' => $referrer['employee_id'] ?? null,
                            'referrer_name' => $referrer['name'],
                            'referrer_email' => $referrer['email'],
                            'referrer_department' => $referrer['department'] ?? null,
                            'referrer_relationship' => $referrer['relationship'] ?? null,
                        ]
                    ),
                ]);

                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'draft_id' => $draft->id,
                ];
            });

            Log::info('リファラル応募を受け付けました', $result);

            return response()->json([
                'success' => true,
                'message' => 'リファラル応募を受け付けました。',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            Log::error('リファラル応募の処理に失敗しました', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'error' => '応募の処理中にエラーが発生しました。',
            ], 500);
        }
    }
}
