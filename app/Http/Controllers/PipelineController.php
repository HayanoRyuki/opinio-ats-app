<?php

namespace App\Http\Controllers;

use App\Models\Job;

class PipelineController extends Controller
{
    public function show(Job $job)
    {
        $steps = $job->selectionSteps()
            ->orderBy('order')
            ->get();

        $applicationsByStep = $steps->mapWithKeys(function ($step) {
            return [
                $step->id => $step->applications()->get(),
            ];
        });

        return view('jobs.pipeline', [
            'job' => $job,
            'steps' => $steps,
            'applicationsByStep' => $applicationsByStep,
        ]);
    }
}
