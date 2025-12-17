<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            JobCategorySeeder::class,
            JobSeeder::class,
            CandidateSeeder::class,
            SelectionStepSeeder::class,
            ApplicationSeeder::class,
        ]);
    }
}
