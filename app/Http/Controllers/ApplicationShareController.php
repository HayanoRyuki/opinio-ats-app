<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationShareController extends Controller
{
    /**
     * 応募者詳細（共有・readonly）
     */
    public function show(Application $application, string $token)
    {
        $job = $application->job;

        // トークン不一致は拒否
        abort_unless(
            $job && $job->share_token === $token,
            403
        );

        return view('applications.show', [
            'application' => $application,
            'readonly'    => true,
        ]);
    }
}
