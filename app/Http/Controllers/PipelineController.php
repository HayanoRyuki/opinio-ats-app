<?php

namespace App\Http\Controllers;

use App\Models\Job;

class PipelineController extends Controller
{
    public function show(Job $job)
    {
        $steps = $job->selectionSteps()
            ->with([
                'applications.candidate',
                'applications.hiringDecision',
                'applications.selectionStep',
            ])
            ->orderBy('order')
            ->get();

        $applicationsByStep = $steps->mapWithKeys(function ($step) {
            return [
                $step->id => $step->applications,
            ];
        });

        return view('jobs.pipeline', [
            'job' => $job,
            'steps' => $steps,
            'applicationsByStep' => $applicationsByStep,
        ]);
    }
}
