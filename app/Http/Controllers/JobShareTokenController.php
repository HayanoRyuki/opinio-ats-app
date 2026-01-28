<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Support\Str;

class JobShareTokenController extends Controller
{
    /**
     * 共有トークン発行
     */
    public function generate(Job $job)
    {
        if (! $job->share_token) {
            $job->share_token = Str::random(32);
            $job->save();
        }

        return redirect()
            ->route('jobs.pipeline', $job)
            ->with('status', '共有URLを発行しました');
    }

    /**
     * 共有URL（readonly パイプライン）
     */
    public function show(Job $job, string $token)
    {
        abort_unless($job->share_token === $token, 403);

        // readonly 表示
        return app(JobPipelineController::class)->show($job)
            ->with('readonly', true);
    }
}
