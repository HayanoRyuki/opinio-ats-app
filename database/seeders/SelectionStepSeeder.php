<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\SelectionStep;

class SelectionStepSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'opinio')->first();
        if (!$company) return;

        $steps = [
            ['key' => 'screening',   'label' => '書類選考', 'order' => 1],
            ['key' => 'interview_1', 'label' => '一次面接', 'order' => 2],
            ['key' => 'interview_2', 'label' => '二次面接', 'order' => 3],
            ['key' => 'final',       'label' => '最終面接', 'order' => 4],
            ['key' => 'offer',       'label' => '内定',     'order' => 5],
            ['key' => 'rejected',    'label' => 'お見送り', 'order' => 99],
        ];

        foreach ($steps as $step) {
            SelectionStep::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'key' => $step['key'],
                ],
                [
                    'label' => $step['label'],
                    'order' => $step['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
