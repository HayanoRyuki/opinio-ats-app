<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Company;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        if (! $company) {
            $this->command->warn('Company not found. Skip JobSeeder.');
            return;
        }

        $jobs = [
            [
                'title' => 'サーバーサイドエンジニア',
                'location' => '東京（リモート可）',
                'employment_type' => 'full_time',
                'salary' => '年収600〜900万円',
                'working_hours' => '10:00〜19:00',
                'description' => 'Laravel を用いた Web アプリケーション開発',
                'requirements' => 'PHP / Laravel の実務経験',
                'benefits' => '社会保険完備、リモート勤務可',
                'notes' => '技術選考あり',
            ],
            [
                'title' => 'プロダクトマネージャー',
                'location' => '東京',
                'employment_type' => 'full_time',
                'salary' => '年収700〜1,000万円',
                'working_hours' => 'フレックス',
                'description' => 'ATS / HR Tech プロダクトの企画推進',
                'requirements' => 'PdM 経験3年以上',
                'benefits' => 'フレックス、書籍購入補助',
                'notes' => '',
            ],
        ];

        foreach ($jobs as $data) {
            Job::create(array_merge($data, [
                'company_id' => $company->id,
                'status' => 'open',
            ]));
        }

        $this->command->info('JobSeeder completed.');
    }
}
