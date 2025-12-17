<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\SelectionStep;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $job = Job::first();
        $candidate = Candidate::first();

        if (!$company || !$job || !$candidate) {
            return;
        }

        // 書類選考ステップを取得
        $step = SelectionStep::where('key', 'screening')->first();

        Application::firstOrCreate(
            [
                'job_id' => $job->id,
                'candidate_id' => $candidate->id,
            ],
            [
                'company_id' => $company->id,
                'selection_step_id' => $step?->id,
                'status' => 'screening',
            ]
        );
    }
}
