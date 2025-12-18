<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SelectionStep;

class PipelineController extends Controller
{
    public function show(Job $job)
    {
        logger()->info('=== Pipeline Debug START ===');
        logger()->info('job_id', [$job->id]);
        logger()->info('job_company_id', [$job->company_id]);

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

        // ★ Job → applications の事実確認
        logger()->info(
            'job->applications count',
            [$job->applications->count()]
        );

        logger()->info(
            'job->applications raw',
            $job->applications->map(fn ($a) => [
                'id' => $a->id,
                'job_id' => $a->job_id,
                'company_id' => $a->company_id,
                'selection_step_id' => $a->selection_step_id,
            ])->toArray()
        );

        // 選考ステップ一覧
        $steps = $job->selectionSteps()
            ->orderBy('order')
            ->get();

        logger()->info(
            'selection steps',
            $steps->map(fn ($s) => [
                'id' => $s->id,
                'company_id' => $s->company_id,
                'order' => $s->order,
                'label' => $s->label,
            ])->toArray()
        );

        // ステップごとに応募者をまとめる
        $applicationsByStep = $job->applications
            ->groupBy('selection_step_id');

        logger()->info(
            'applicationsByStep keys',
            array_keys($applicationsByStep->toArray())
        );

        logger()->info('=== Pipeline Debug END ===');

        return view('jobs.pipeline', [
            'job' => $job,
            'steps' => $steps,
            'applicationsByStep' => $applicationsByStep,
        ]);
    }
}
