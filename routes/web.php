<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobPipelineController;
use App\Http\Controllers\JobShareTokenController;
use App\Http\Controllers\ApplicationShareController;

/*
|--------------------------------------------------------------------------
| Root / Login
|--------------------------------------------------------------------------
|
| JWT が無ければ SSO 開始
| JWT があれば dashboard へ
|
*/
Route::middleware(['require.sso'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/login', function () {
        return redirect()->route('dashboard');
    });
});

/*
|--------------------------------------------------------------------------
| Public routes（SSO callback のみ）
|--------------------------------------------------------------------------
*/
require __DIR__ . '/sso.php';

/*
|--------------------------------------------------------------------------
| Protected routes（JWT 必須）
|--------------------------------------------------------------------------
*/
Route::middleware(['verify.jwt'])->group(function () {

    // 静的ページ
    Route::view('/terms', 'static.terms')->name('terms');
    Route::view('/privacy', 'static.privacy')->name('privacy');
    Route::view('/data-policy', 'static.data-policy')->name('data-policy');
    Route::view('/ai-policy', 'static.ai-policy')->name('ai-policy');
    Route::view('/company', 'static.company')->name('company');
    Route::view('/help', 'static.help')->name('help');

    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Evaluations / Analytics（仮）
    |--------------------------------------------------------------------------
    */
    Route::view('/evaluations', 'evaluations.index')
        ->name('evaluations.index');

    Route::view('/analytics', 'analytics.index')
        ->name('analytics.index');

    /*
    |--------------------------------------------------------------------------
    | Candidates
    |--------------------------------------------------------------------------
    */
    Route::view('/candidates', 'candidates.index')
        ->name('candidates.index');

    /*
    |--------------------------------------------------------------------------
    | Job Pipeline
    |--------------------------------------------------------------------------
    */
    Route::get('/jobs/{job}/pipeline', [JobPipelineController::class, 'show'])
        ->name('jobs.pipeline');

    Route::post('/jobs/{job}/share-token', [JobShareTokenController::class, 'generate'])
        ->name('jobs.share-token.generate');

    Route::get('/jobs/{job}/pipeline/share/{token}', [JobShareTokenController::class, 'show'])
        ->name('jobs.pipeline.share');

    /*
    |--------------------------------------------------------------------------
    | Application Share
    |--------------------------------------------------------------------------
    */
    Route::get(
        '/applications/{application}/share/{token}',
        [ApplicationShareController::class, 'show']
    )->name('applications.share');

    require __DIR__ . '/admin.php';
    require __DIR__ . '/interviewer.php';
});
