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

            // TODO: 新しいスキーマに合わせて修正が必要
            // SelectionStepSeeder::class,

            // 求人
            // JobSeeder::class,

            // 応募者
            // CandidateSeeder::class,

            // 応募（pipeline 用）
            // ApplicationSeeder::class,
        ]);
    }
}
