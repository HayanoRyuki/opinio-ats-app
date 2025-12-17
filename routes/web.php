<?php

use Illuminate\Support\Facades\Route;
use App\Models\Job;

use App\Http\Controllers\JobController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\SharePipelineController;
use App\Http\Controllers\ShareApplicationController;
use App\Http\Controllers\ApplicationStepController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EvaluationController;

/*
|--------------------------------------------------------------------------
| Top
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/jobs');
});

/*
|--------------------------------------------------------------------------
| 認証必須：recruiter 管理画面
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 求人
    Route::resource('jobs', JobController::class)->only([
        'index',
        'create',
        'store',
    ]);

    // 求人パイプライン
    Route::get(
        '/jobs/{job}/pipeline',
        [PipelineController::class, 'show']
    )->name('jobs.pipeline');

    // 応募者
    Route::get(
        '/jobs/{job}/applications/create',
        [ApplicationController::class, 'create']
    )->name('applications.create');

    Route::post(
        '/jobs/{job}/applications',
        [ApplicationController::class, 'store']
    )->name('applications.store');

    Route::patch(
        '/applications/{application}/step',
        [ApplicationStepController::class, 'update']
    )->name('applications.step.update');

    // 評価（★ここを統合）
    Route::get(
        '/applications/{application}/evaluations/create',
        [EvaluationController::class, 'create']
    )->name('evaluations.create');

    Route::post(
        '/applications/{application}/evaluations',
        [EvaluationController::class, 'store']
    )->name('evaluations.store');

    // 共有トークン発行
    Route::post(
        '/jobs/{job}/share-token',
        function (Job $job) {
            $job->generateShareToken();
            return back();
        }
    )->name('jobs.share-token.generate');
});

/*
|--------------------------------------------------------------------------
| 共有（ログイン不要・readonly）
|--------------------------------------------------------------------------
*/

// 求人パイプライン共有
Route::get(
    '/share/jobs/{job}/{token}',
    [SharePipelineController::class, 'show']
)->name('jobs.pipeline.share');

// 応募者単体共有
Route::get(
    '/share/applications/{application}/{token}',
    [ShareApplicationController::class, 'show']
)->name('applications.share');

/*
|--------------------------------------------------------------------------
| Debug
|--------------------------------------------------------------------------
*/
Route::get('/__route_test', function () {
    return 'route ok';
});

Route::get('/login', function () {
    return <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログインが必要です</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 0;
        }
        .wrap {
            max-width: 480px;
            margin: 80px auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 32px;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 16px;
        }
        p {
            font-size: 14px;
            color: #374151;
            line-height: 1.6;
        }
        .note {
            margin-top: 20px;
            padding: 12px;
            background: #f3f4f6;
            border-radius: 6px;
            font-size: 13px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>ログインが必要です</h1>

        <p>
            このページは、採用担当者向けの管理画面です。<br>
            続行するにはログインが必要です。
        </p>

        <div class="note">
            ※ 現在この ATS は開発・検証中のため、<br>
            実際のログイン機能は未実装です。
        </div>
    </div>
</body>
</html>
HTML;
})->name('login');
