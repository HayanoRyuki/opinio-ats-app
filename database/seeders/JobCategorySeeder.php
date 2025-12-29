<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\JobCategory;

class JobCategorySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) {
            return;
        }

        $categories = [
            [
                'key' => 'business',
                'label' => 'ビジネス',
                'order' => 1,
            ],
            [
                'key' => 'engineer',
                'label' => 'エンジニア',
                'order' => 2,
            ],
            [
                'key' => 'corporate',
                'label' => 'コーポレート',
                'order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            JobCategory::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'key' => $category['key'],
                ],
                [
                    'label' => $category['label'],
                    'order' => $category['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
