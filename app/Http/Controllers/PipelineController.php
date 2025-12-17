<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SelectionStep;

class PipelineController extends Controller
{
    public function show(Job $job)
{
    $job->load([
        'applications.candidate',
        'applications.selectionStep',
    ]);

    $steps = $job->selectionSteps()->orderBy('order')->get();

    $applicationsByStep = $job->applications
        ->groupBy('selection_step_id');

    return view('jobs.pipeline', [
        'job' => $job,
        'steps' => $steps,
        'applicationsByStep' => $applicationsByStep,
    ]);
}

}
