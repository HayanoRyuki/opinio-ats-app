<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\SelectionStep;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $job = Job::first();
        $steps = SelectionStep::orderBy('order')->get();

        Candidate::take(5)->get()->each(function ($candidate, $index) use ($job, $steps) {
            Application::firstOrCreate([
                'company_id' => $job->company_id,
                'job_id' => $job->id,
                'candidate_id' => $candidate->id,
            ], [
                'status' => $steps[$index % $steps->count()]->key,
            ]);
        });
    }
}
