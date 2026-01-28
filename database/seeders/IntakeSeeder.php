<?php

namespace Database\Seeders;

use App\Models\ApplicationIntake;
use App\Models\Company;
use App\Models\IntakeCandidateDraft;
use App\Models\Job;
use Illuminate\Database\Seeder;

class IntakeSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        if (! $company) {
            $this->command->error('先に DatabaseSeeder を実行してください。');

            return;
        }

        // テスト用求人を作成
        $job = Job::create([
            'company_id' => $company->id,
            'title' => 'フルスタックエンジニア',
            'description' => 'Webアプリケーションの開発をお任せします。',
            'requirements' => 'PHP/Laravel または Node.js の経験3年以上',
            'status' => 'open',
            'published_at' => now(),
        ]);

        // 直接応募
        $intake1 = ApplicationIntake::create([
            'company_id' => $company->id,
            'job_id' => $job->id,
            'channel' => 'direct',
            'status' => 'draft',
            'raw_data' => [
                'name' => '山田 太郎',
                'email' => 'yamada@example.com',
                'phone' => '090-1234-5678',
                'message' => 'エンジニア職に応募します。よろしくお願いします。',
            ],
            'parsed_data' => [
                'name' => '山田 太郎',
                'email' => 'yamada@example.com',
                'phone' => '090-1234-5678',
            ],
            'received_at' => now()->subHours(2),
        ]);

        IntakeCandidateDraft::create([
            'application_intake_id' => $intake1->id,
            'status' => 'draft',
            'name' => '山田 太郎',
            'email' => 'yamada@example.com',
            'phone' => '090-1234-5678',
            'extracted_data' => [
                'experience' => '5年',
                'skills' => ['PHP', 'Laravel', 'Vue.js'],
            ],
        ]);

        // エージェント推薦
        $intake2 = ApplicationIntake::create([
            'company_id' => $company->id,
            'job_id' => $job->id,
            'channel' => 'agent',
            'status' => 'draft',
            'raw_data' => [
                'agent_company' => 'リクルートエージェント',
                'agent_name' => '佐藤 花子',
                'candidate' => [
                    'name' => '鈴木 一郎',
                    'email' => 'suzuki@example.com',
                    'current_company' => '株式会社ABC',
                    'current_position' => 'シニアエンジニア',
                ],
                'recommendation' => '即戦力として活躍いただける優秀な方です。',
            ],
            'parsed_data' => [
                'name' => '鈴木 一郎',
                'email' => 'suzuki@example.com',
            ],
            'received_at' => now()->subHours(5),
        ]);

        IntakeCandidateDraft::create([
            'application_intake_id' => $intake2->id,
            'status' => 'draft',
            'name' => '鈴木 一郎',
            'email' => 'suzuki@example.com',
            'extracted_data' => [
                'current_company' => '株式会社ABC',
                'current_position' => 'シニアエンジニア',
                'agent' => 'リクルートエージェント / 佐藤 花子',
            ],
        ]);

        // リファラル
        $intake3 = ApplicationIntake::create([
            'company_id' => $company->id,
            'job_id' => $job->id,
            'channel' => 'referral',
            'status' => 'draft',
            'raw_data' => [
                'referrer' => '田中 次郎（開発部）',
                'referred' => [
                    'name' => '高橋 美咲',
                    'email' => 'takahashi@example.com',
                    'phone' => '080-9876-5432',
                ],
                'relationship' => '前職の同僚',
                'recommendation' => 'とても優秀なエンジニアで、チームワークも抜群です。',
            ],
            'parsed_data' => [
                'name' => '高橋 美咲',
                'email' => 'takahashi@example.com',
                'phone' => '080-9876-5432',
            ],
            'received_at' => now()->subDay(),
        ]);

        IntakeCandidateDraft::create([
            'application_intake_id' => $intake3->id,
            'status' => 'draft',
            'name' => '高橋 美咲',
            'email' => 'takahashi@example.com',
            'phone' => '080-9876-5432',
            'extracted_data' => [
                'referrer' => '田中 次郎（開発部）',
                'relationship' => '前職の同僚',
            ],
        ]);

        // メディア経由（確定済みサンプル）
        $intake4 = ApplicationIntake::create([
            'company_id' => $company->id,
            'job_id' => $job->id,
            'channel' => 'media',
            'status' => 'confirmed',
            'raw_data' => [
                'source' => 'Wantedly',
                'name' => '伊藤 健太',
                'email' => 'ito@example.com',
            ],
            'parsed_data' => [
                'name' => '伊藤 健太',
                'email' => 'ito@example.com',
            ],
            'received_at' => now()->subDays(3),
        ]);

        $this->command->info('テスト用取り込みデータを作成しました（3件のドラフト）');
    }
}
