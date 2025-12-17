<?php

namespace App\Http\Controllers;

use App\Models\Application;

class ShareApplicationController extends Controller
{
    /**
     * 応募者単体・共有（readonly）ビュー
     */
    public function show(Application $application, string $token)
    {
        // 応募が属する求人
        $job = $application->job;

        // Job の share_token で検証
        if (!$job || $job->share_token !== $token) {
            abort(404);
        }

        // 必要な関連をロード
        $application->load([
            'candidate',
            'selectionStep',
        ]);

        $readonly = true;

        return view('applications.share', compact(
            'application',
            'job',
            'readonly'
        ));
    }
}
