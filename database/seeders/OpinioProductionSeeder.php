<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Job;
use App\Models\Person;
use App\Models\Candidate;
use App\Models\Application;
use Carbon\Carbon;

/**
 * 本番用シーダー: Opinio社の求人と応募データを作成
 *
 * 使用方法:
 * php artisan db:seed --class=OpinioProductionSeeder
 */
class OpinioProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Opinio社を取得（本番では1社のみ想定）
        $company = Company::first();

        if (!$company) {
            $this->command->error('Company not found.');
            return;
        }

        $this->command->info("Company: {$company->name} ({$company->id})");

        // === 求人3件作成 ===
        $jobsData = [
            [
                'title' => '営業（法人向けSaaS）',
                'description' => "【仕事内容】\n・新規顧客開拓（インバウンド中心）\n・既存顧客へのアップセル提案\n・顧客課題のヒアリングとソリューション提案\n\n【求める人物像】\n・法人営業経験2年以上\n・SaaS業界での経験歓迎",
                'status' => 'open',
            ],
            [
                'title' => 'バックエンドエンジニア（Laravel/PHP）',
                'description' => "【仕事内容】\n・自社プロダクトの設計・開発\n・APIの設計・実装\n・パフォーマンス改善\n\n【必須スキル】\n・PHP/Laravel経験3年以上\n・RDBMSの設計経験\n\n【歓迎スキル】\n・Vue.js経験\n・AWSの運用経験",
                'status' => 'open',
            ],
            [
                'title' => 'カスタマーサクセス（CS）',
                'description' => "【仕事内容】\n・導入企業のオンボーディング支援\n・利用状況モニタリングと活用提案\n・顧客の声をプロダクトチームへフィードバック\n\n【求める人物像】\n・顧客対応経験（営業・サポート等）\n・SaaS業界に興味がある方",
                'status' => 'open',
            ],
        ];

        $jobs = [];
        foreach ($jobsData as $data) {
            $job = Job::create([
                'company_id' => $company->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status'],
                'published_at' => Carbon::now(),
            ]);
            $jobs[] = $job;
            $this->command->info("Created job: {$job->title}");
        }

        // === 応募者3件作成（各求人に1件ずつ）===
        $applicantsData = [
            [
                'name' => '田中 太郎',
                'name_kana' => 'タナカ タロウ',
                'email' => 'tanaka.taro@example.com',
                'phone' => '090-1234-5678',
                'source_channel' => 'direct',
                'job_index' => 0, // 営業
            ],
            [
                'name' => '鈴木 花子',
                'name_kana' => 'スズキ ハナコ',
                'email' => 'suzuki.hanako@example.com',
                'phone' => '090-2345-6789',
                'source_channel' => 'scout',
                'job_index' => 1, // エンジニア
            ],
            [
                'name' => '佐藤 健一',
                'name_kana' => 'サトウ ケンイチ',
                'email' => 'sato.kenichi@example.com',
                'phone' => '090-3456-7890',
                'source_channel' => 'agent',
                'job_index' => 2, // CS
            ],
        ];

        foreach ($applicantsData as $data) {
            // Person作成
            $person = Person::create([
                'name' => $data['name'],
                'name_kana' => $data['name_kana'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            // Candidate作成
            $candidate = Candidate::create([
                'company_id' => $company->id,
                'person_id' => $person->id,
                'source_channel' => $data['source_channel'],
            ]);

            // Application作成
            $job = $jobs[$data['job_index']];
            $application = Application::create([
                'candidate_id' => $candidate->id,
                'job_id' => $job->id,
                'status' => 'active',
                'applied_at' => Carbon::now(),
            ]);

            $this->command->info("Created application: {$data['name']} → {$job->title}");
        }

        $this->command->info('OpinioProductionSeeder completed!');
    }
}
