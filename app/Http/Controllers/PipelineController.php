<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SelectionStep;

class PipelineController extends Controller
{
    public function show(Job $job)
    {
        // 求人に紐づく応募者をまとめて読み込む（★ evaluations を追加）
        $job->load([
            'applications.candidate',
            'applications.selectionStep',
            'applications.evaluations', // ← ★ここだけ追加
        ]);

        // 選考ステップ一覧
        $steps = $job->selectionSteps()
            ->orderBy('order')
            ->get();

        // ステップごとに応募者をまとめる
        $applicationsByStep = $job->applications
            ->groupBy('selection_step_id');

        return view('jobs.pipeline', [
            'job' => $job,
            'steps' => $steps,
            'applicationsByStep' => $applicationsByStep,
        ]);
    }
}
