<?php

namespace App\Http\Controllers;

use App\Models\Application;

class ShareApplicationController extends Controller
{
    public function show(Application $application, string $token)
    {
        // トークンチェック（簡易）
        if ($application->job->share_token !== $token) {
            abort(403);
        }

        // 全評価を最新順でロード
        $application->load([
            'candidate',
            'job',
            'selectionStep',
            'evaluations' => function ($q) {
                $q->with('user')->orderBy('created_at', 'desc');
            },
        ]);

        return view('applications.share', [
            'application' => $application,
            'job'         => $application->job,
            'readonly'    => true,
        ]);
    }
}
