<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Seeders\{
    CompanySeeder,
    JobCategorySeeder,
    JobSeeder,
    CandidateSeeder,
    SelectionStepSeeder,
    ApplicationSeeder,
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            JobCategorySeeder::class,

            // 会社単位の選考フローを先に作る
            SelectionStepSeeder::class,

            // 求人
            JobSeeder::class,

            // 応募者
            CandidateSeeder::class,

            // 応募（pipeline 用）
            ApplicationSeeder::class,
        ]);
    }
}
