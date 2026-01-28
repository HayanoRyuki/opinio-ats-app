<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\SelectionStep;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = Job::all();

        if ($jobs->isEmpty()) {
            $this->command->warn('No jobs found. Skip ApplicationSeeder.');
            return;
        }

        $candidates = Candidate::all();

        if ($candidates->isEmpty()) {
            $this->command->warn('No candidates found. Skip ApplicationSeeder.');
            return;
        }

        foreach ($jobs as $job) {

            // 会社フロー（SelectionStep）
            $steps = SelectionStep::where('company_id', $job->company_id)
                ->orderBy('order')
                ->get();

            if ($steps->isEmpty()) {
                $this->command->warn("No selection steps for job {$job->id}");
                continue;
            }

            // 候補者を適当に3〜5人割り当て
            $targetCandidates = $candidates->shuffle()->take(rand(3, 5));

            foreach ($targetCandidates as $index => $candidate) {

                // 初期 or 少し進んだステップに配置
                $step = $steps->get(min($index, $steps->count() - 1));

                Application::create([
                    'company_id'        => $job->company_id,
                    'job_id'            => $job->id,
                    'candidate_id'      => $candidate->id,
                    'selection_step_id' => $step->id,
                    'status'            => $step->key ?? $step->label ?? 'in_progress',
                ]);
            }
        }

        $this->command->info('ApplicationSeeder completed.');
    }
}
