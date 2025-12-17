<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\Job;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) {
            return;
        }

        $category = JobCategory::where('company_id', $company->id)->first();
        if (!$category) {
            return;
        }

        Job::firstOrCreate(
            [
                'company_id' => $company->id,
                'title' => 'サンプル求人',
            ],
            [
                'job_category_id' => $category->id,
                'description' => 'Opinio ATS のデモ用求人です。',
                'status' => 'open',
            ]
        );
    }
}
