<?php

namespace App\Http\Controllers;

use App\Models\Job;

class SharePipelineController extends Controller
{
    /**
     * ATS共有用（readonly）パイプライン表示
     */
    public function show(Job $job, string $token)
    {
        // トークン不一致は 404
        if ($job->share_token !== $token) {
            abort(404);
        }

        // 表示に必要なリレーションをロード
        $job->load([
            'applications.candidate',
            'applications.selectionStep',
        ]);

        // 会社共通ステップ
        $steps = $job->selectionSteps()->orderBy('order')->get();

        // readonly フラグ
        $readonly = true;

        return view('jobs.pipeline', compact(
            'job',
            'steps',
            'readonly'
        ));
    }
}
