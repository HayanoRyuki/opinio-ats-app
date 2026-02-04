<?php

namespace Database\Seeders;

use App\Enums\IntakeChannel;
use App\Enums\IntakeStatus;
use App\Models\ApplicationIntake;
use App\Models\Company;
use App\Models\IntakeCandidateDraft;
use App\Models\Job;
use Illuminate\Database\Seeder;

/**
 * 4チャネル取り込みサンプルデータ
 *
 * 本番で動作確認用に使用
 */
class IntakeSampleSeeder extends Seeder
{
    public function run(): void
    {
        // Opinio社を取得（なければ最初の会社）
        $company = Company::where('name', 'like', '%Opinio%')->first()
            ?? Company::first();

        if (!$company) {
            $this->command->error('会社が見つかりません。先に会社を作成してください。');
            return;
        }

        // 募集中の求人を取得
        $job = Job::where('company_id', $company->id)
            ->where('status', 'open')
            ->first();

        $this->command->info("会社: {$company->name}");
        $this->command->info("求人: " . ($job?->title ?? 'なし'));

        // 1. 直接応募（自社採用サイト）
        $this->createDirectIntake($company, $job);

        // 2. スカウト（ビズリーチ）- 仮応募
        $this->createScoutIntake($company, $job, 'ビズリーチ');

        // 3. スカウト（Wantedly）- 仮応募
        $this->createScoutIntake($company, $job, 'Wantedly');

        // 4. エージェント推薦
        $this->createAgentIntake($company, $job);

        // 5. リファラル（社員紹介）
        $this->createReferralIntake($company, $job);

        $this->command->info('4チャネルのサンプルデータを作成しました。');
    }

    private function createDirectIntake(Company $company, ?Job $job): void
    {
        $intake = ApplicationIntake::create([
            'company_id' => $company->id,
            'job_id' => $job?->id,
            'channel' => IntakeChannel::DIRECT,
            'status' => IntakeStatus::DRAFT,
            'is_preliminary' => false,
            'source_id' => 'web_sample_' . uniqid(),
            'raw_data' => [
                'source' => '採用サイト',
                'form_id' => 'career-form-001',
            ],
            'received_at' => now()->subHours(2),
        ]);

        IntakeCandidateDraft::create([
            'application_intake_id' => $intake->id,
            'status' => IntakeStatus::DRAFT,
            'is_preliminary' => false,
            'name' => '山田 太郎',
            'email' => 'yamada.taro@example.com',
            'phone' => '090-1234-5678',
            'extracted_data' => [
                'resume_url' => 'https://example.com/resume/yamada.pdf',
                'cover_letter' => '貴社の採用管理システム開発に興味があり応募しました。',
                'current_company' => '株式会社テスト',
                'years_experience' => 5,
            ],
        ]);

        $this->command->info('✓ 直接応募: 山田太郎');
    }

    private function createScoutIntake(Company $company, ?Job $job, string $service): void
    {
        $candidates = [
            'ビズリーチ' => [
                'name' => '鈴木 花子',
                'email' => 'suzuki.hanako@example.com',
                'phone' => '080-2345-6789',
                'profile_url' => 'https://bizreach.jp/profile/suzuki123',
            ],
            'Wantedly' => [
                'name' => '佐藤 健一',
                'email' => 'sato.kenichi@example.com',
                'phone' => '070-3456-7890',
                'profile_url' => 'https://wantedly.com/users/sato456',
            ],
        ];

        $candidate = $candidates[$service];

        $intake = ApplicationIntake::create([
            'company_id' => $company->id,
            'job_id' => $job?->id,
            'channel' => IntakeChannel::SCOUT,
            'status' => IntakeStatus::DRAFT,
            'is_preliminary' => true, // 仮応募
            'source_id' => 'scout_' . strtolower(str_replace(' ', '', $service)) . '_' . uniqid(),
            'raw_data' => [
                'scout_service' => $service,
                'response_type' => 'interested',
                'scout_sent_at' => now()->subDays(3)->toIso8601String(),
            ],
            'received_at' => now()->subHours(5),
        ]);

        IntakeCandidateDraft::create([
            'application_intake_id' => $intake->id,
            'status' => IntakeStatus::DRAFT,
            'is_preliminary' => true, // 仮応募
            'name' => $candidate['name'],
            'email' => $candidate['email'],
            'phone' => $candidate['phone'],
            'extracted_data' => [
                'profile_url' => $candidate['profile_url'],
                'scout_service' => $service,
                'response_type' => 'interested',
                'current_title' => 'シニアエンジニア',
            ],
        ]);

        $this->command->info("✓ スカウト（{$service}・仮応募）: {$candidate['name']}");
    }

    private function createAgentIntake(Company $company, ?Job $job): void
    {
        $intake = ApplicationIntake::create([
            'company_id' => $company->id,
            'job_id' => $job?->id,
            'channel' => IntakeChannel::AGENT,
            'status' => IntakeStatus::DRAFT,
            'is_preliminary' => false,
            'source_id' => 'agent_sample_' . uniqid(),
            'raw_data' => [
                'agent_company' => 'リクルートエージェント',
                'agent_name' => '田中 美咲',
                'agent_email' => 'tanaka@recruit-agent.example.com',
            ],
            'received_at' => now()->subHours(8),
        ]);

        IntakeCandidateDraft::create([
            'application_intake_id' => $intake->id,
            'status' => IntakeStatus::DRAFT,
            'is_preliminary' => false,
            'name' => '高橋 誠',
            'email' => 'takahashi.makoto@example.com',
            'phone' => '090-4567-8901',
            'extracted_data' => [
                'resume_url' => 'https://example.com/resume/takahashi.pdf',
                'recommendation' => '即戦力として活躍できる人材です。リーダー経験もあり、チームマネジメントも可能。',
                'agent_company' => 'リクルートエージェント',
                'agent_name' => '田中 美咲',
                'agent_email' => 'tanaka@recruit-agent.example.com',
                'current_company' => '大手SIer',
                'current_salary' => '年収700万円',
                'desired_salary' => '年収800万円以上',
            ],
        ]);

        $this->command->info('✓ エージェント: 高橋誠（リクルートエージェント経由）');
    }

    private function createReferralIntake(Company $company, ?Job $job): void
    {
        $intake = ApplicationIntake::create([
            'company_id' => $company->id,
            'job_id' => $job?->id,
            'channel' => IntakeChannel::REFERRAL,
            'status' => IntakeStatus::DRAFT,
            'is_preliminary' => false,
            'source_id' => 'referral_sample_' . uniqid(),
            'raw_data' => [
                'referrer_name' => '早野 龍輝',
                'referrer_email' => 'hayano@media-confidence.com',
                'referrer_department' => '開発部',
                'relationship' => '元同僚',
            ],
            'received_at' => now()->subHours(1),
        ]);

        IntakeCandidateDraft::create([
            'application_intake_id' => $intake->id,
            'status' => IntakeStatus::DRAFT,
            'is_preliminary' => false,
            'name' => '伊藤 大輔',
            'email' => 'ito.daisuke@example.com',
            'phone' => '080-5678-9012',
            'extracted_data' => [
                'recommendation' => '前職で一緒に働いていました。技術力が高く、チームワークも良い人材です。',
                'referrer_employee_id' => 'EMP001',
                'referrer_name' => '早野 龍輝',
                'referrer_email' => 'hayano@media-confidence.com',
                'referrer_department' => '開発部',
                'referrer_relationship' => '元同僚',
                'current_company' => 'スタートアップ企業',
            ],
        ]);

        $this->command->info('✓ リファラル: 伊藤大輔（早野龍輝からの紹介）');
    }
}
