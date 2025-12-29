<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    ApplicationController,
    CandidateController,
    JobController,
    PipelineController,
    InterviewController,
    ReportController
};

/*
|--------------------------------------------------------------------------
| 公開ページ
|--------------------------------------------------------------------------
*/

// テスト用 JWT 認証確認ルート
Route::middleware(['require.sso','verify.jwt'])->get('/__jwt_test', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'user' => auth()->user(),
        'company_id' => $request->attributes->get('company_id'),
        'role' => $request->attributes->get('role'),
        'jwt' => $request->attributes->get('jwt'),
    ]);
});

// SSO callback（DEV環境のみ有効）
Route::get('/sso/callback', \App\Http\Controllers\SsoCallbackController::class);

/*
|--------------------------------------------------------------------------
| 保護ルート（JWT + SSO 適用）
|--------------------------------------------------------------------------
*/
Route::middleware(['require.sso', 'verify.jwt'])->group(function () {

    // ダッシュボード関連
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // 応募 / 候補者関連
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);
    Route::get('/candidates', [CandidateController::class, 'index']);
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show']);

    // 募集 / 求人関連
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{job}', [JobController::class, 'show']);

    // 選考 / パイプライン / 日程 / メッセージ
    Route::get('/pipeline', [PipelineController::class, 'index']);
    Route::get('/pipeline/{pipeline}', [PipelineController::class, 'show']);
    Route::get('/interviews', [InterviewController::class, 'index']);
    Route::get('/interviews/{interview}', [InterviewController::class, 'show']);

    // 分析 / レポート
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
});
