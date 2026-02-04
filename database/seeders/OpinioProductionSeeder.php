<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // === 既存の求人を取得 ===
        $jobs = Job::where('company_id', $company->id)->get();

        if ($jobs->isEmpty()) {
            $this->command->error('No jobs found. Please create jobs first.');
            return;
        }

        $this->command->info("Found {$jobs->count()} jobs");

        // === 応募者3件作成（本番のcandidates構造に合わせる）===
        $applicantsData = [
            [
                'name' => '田中 太郎',
                'email' => 'tanaka.taro@example.com',
                'phone' => '090-1234-5678',
                'source_channel' => 'direct',
                'job_index' => 0,
            ],
            [
                'name' => '鈴木 花子',
                'email' => 'suzuki.hanako@example.com',
                'phone' => '090-2345-6789',
                'source_channel' => 'scout',
                'job_index' => 1,
            ],
            [
                'name' => '佐藤 健一',
                'email' => 'sato.kenichi@example.com',
                'phone' => '090-3456-7890',
                'source_channel' => 'agent',
                'job_index' => 2,
            ],
        ];

        foreach ($applicantsData as $data) {
            $jobIndex = min($data['job_index'], $jobs->count() - 1);
            $job = $jobs[$jobIndex];

            // Candidate作成（本番構造: name, email, phoneを直接持つ）
            $candidateId = DB::table('candidates')->insertGetId([
                'company_id' => $company->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'source_channel' => $data['source_channel'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Application作成（本番構造: company_idあり、applied_atなし）
            DB::table('applications')->insert([
                'company_id' => $company->id,
                'candidate_id' => $candidateId,
                'job_id' => $job->id,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->command->info("Created: {$data['name']} → {$job->title}");
        }

        $this->command->info('OpinioProductionSeeder completed!');
    }
}
