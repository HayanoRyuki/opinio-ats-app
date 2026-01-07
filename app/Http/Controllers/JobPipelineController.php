<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use App\Models\SelectionStep;

class JobPipelineController extends Controller
{
    /**
     * 求人別 選考パイプライン表示
     */
    public function show(Job $job)
    {
        // 選考ステップ（順序付き）
        $steps = SelectionStep::orderBy('order')->get();

        // 応募者をステップ別に取得
        $applicationsByStep = Application::with([
                'candidate',
                'selectionStep',
                'hiringDecision',
            ])
            ->where('job_id', $job->id)
            ->get()
            ->groupBy('selection_step_id');

        return view('jobs.pipeline', [
            'job' => $job,
            'steps' => $steps,
            'applicationsByStep' => $applicationsByStep,
            'readonly' => false,
        ]);
    }
}
