<?php

use App\Http\Controllers\Api\IntakeApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| 全 API は /api プレフィックス付きで公開される。
| 認証は Sanctum トークンを使用。
|
*/

// ========================================
// 公開API（APIキー認証 — オプショナル）
// ========================================
Route::prefix('intake')->name('api.intake.')->middleware('intake.apikey')->group(function () {
    Route::post('/web', [IntakeApiController::class, 'web'])->name('web');
    Route::post('/agent', [IntakeApiController::class, 'agent'])->name('agent');
    Route::post('/scout', [IntakeApiController::class, 'scout'])->name('scout');
    Route::post('/referral', [IntakeApiController::class, 'referral'])->name('referral');
});

// ========================================
// 認証済みAPI（Sanctum）
// ========================================
Route::middleware('auth:sanctum')->group(function () {
    // 候補者取り込み
    Route::prefix('intake')->name('intake.')->group(function () {
        // ApplicationIntake (共通)
        Route::apiResource('applications', \App\Http\Controllers\Api\ApplicationIntakeController::class);

        // 直接応募
        Route::post('direct', [\App\Http\Controllers\Api\DirectApplicationController::class, 'store'])
            ->name('direct.store');

        // メディア経由
        Route::post('media', [\App\Http\Controllers\Api\MediaApplicationController::class, 'store'])
            ->name('media.store');

        // エージェント推薦
        Route::apiResource('recommendations', \App\Http\Controllers\Api\RecommendationController::class);

        // リファラル
        Route::apiResource('referrals', \App\Http\Controllers\Api\ReferralIntakeController::class);
    });

    // 候補者ドラフト確認
    Route::prefix('drafts')->name('drafts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\IntakeCandidateDraftController::class, 'index'])
            ->name('index');
        Route::get('{draft}', [\App\Http\Controllers\Api\IntakeCandidateDraftController::class, 'show'])
            ->name('show');
        Route::post('{draft}/confirm', [\App\Http\Controllers\Api\IntakeCandidateDraftController::class, 'confirm'])
            ->name('confirm');
        Route::post('{draft}/reject', [\App\Http\Controllers\Api\IntakeCandidateDraftController::class, 'reject'])
            ->name('reject');
    });
});
