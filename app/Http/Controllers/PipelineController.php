<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SelectionStep;

class PipelineController extends Controller
{
    public function show(Job $job)
    {
        // ★ 直近で触った求人をセッションに保存
        session([
            'last_job_id' => $job->id,
            'last_job_title' => $job->title,
        ]);

        // 求人に紐づく応募者をまとめて読み込む（evaluations 含む）
        $job->load([
            'applications.candidate',
            'applications.selectionStep',
            'applications.evaluations',
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
