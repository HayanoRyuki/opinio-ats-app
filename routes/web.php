<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobPipelineController;
use App\Http\Controllers\JobShareTokenController;

/*
|--------------------------------------------------------------------------
| Root / Login
|--------------------------------------------------------------------------
*/
$redirectToAuth = function () {
    return redirect()->away(
        'https://auth.opinio.co.jp/sso/start'
        . '?client_id=ats'
        . '&redirect_uri=' . urlencode('https://ats.opinio.co.jp/sso/callback')
    );
};

Route::get('/', $redirectToAuth);
Route::get('/login', $redirectToAuth);

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/sso.php';

/*
|--------------------------------------------------------------------------
| Protected routes（JWT はここで一度だけ）
|--------------------------------------------------------------------------
*/
Route::middleware(['verify.jwt'])->group(function () {

    // 共通静的ページ
    Route::view('/terms', 'static.terms')->name('terms');
    Route::view('/privacy', 'static.privacy')->name('privacy');
    Route::view('/data-policy', 'static.data-policy')->name('data-policy');
    Route::view('/ai-policy', 'static.ai-policy')->name('ai-policy');
    Route::view('/company', 'static.company')->name('company');
    Route::view('/help', 'static.help')->name('help');

    // 管理者・採用担当ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Job Pipeline（本命）
    |--------------------------------------------------------------------------
    */

    // 求人別パイプライン
    Route::get('/jobs/{job}/pipeline', [JobPipelineController::class, 'show'])
        ->name('jobs.pipeline');

    // 共有トークン発行
    Route::post('/jobs/{job}/share-token', [JobShareTokenController::class, 'generate'])
        ->name('jobs.share-token.generate');

    // 共有URL（readonly パイプライン）
    Route::get('/jobs/{job}/pipeline/share/{token}', [JobShareTokenController::class, 'show'])
        ->name('jobs.pipeline.share');

    // 管理者系
    require __DIR__ . '/admin.php';

    // 面接官系
    require __DIR__ . '/interviewer.php';
});
