<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Web\CandidateController;
use App\Http\Controllers\Web\JobController;
use App\Http\Controllers\Web\ApplicationController;
use App\Http\Controllers\Web\IntakeController;
use App\Http\Controllers\Web\CandidateMessageController;
use App\Http\Controllers\Web\ExternalChatImportController;
use App\Http\Controllers\Web\MyPageController;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Root / Login
|--------------------------------------------------------------------------
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

    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 取り込み管理
    Route::get('/intake', [IntakeController::class, 'index'])->name('intake.index');
    Route::get('/intake/drafts', [IntakeController::class, 'drafts'])->name('intake.drafts');
    Route::get('/intake/drafts/{id}', [IntakeController::class, 'draftDetail'])->name('intake.drafts.show');
    Route::post('/intake/drafts/{draft}/confirm', [IntakeController::class, 'confirmDraft'])->name('intake.drafts.confirm');
    Route::post('/intake/drafts/{draft}/confirm-and-promote', [IntakeController::class, 'confirmAndPromoteDraft'])->name('intake.drafts.confirm-and-promote');
    Route::post('/intake/drafts/{draft}/promote', [IntakeController::class, 'promoteDraft'])->name('intake.drafts.promote');
    Route::post('/intake/drafts/{draft}/reject', [IntakeController::class, 'rejectDraft'])->name('intake.drafts.reject');

    // 候補者
    Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])->name('candidates.show');
    Route::post('/candidates/{candidate}/messages', [CandidateMessageController::class, 'store'])->name('candidates.messages.store');
    Route::post('/candidates/{candidate}/external-chat', [ExternalChatImportController::class, 'store'])->name('candidates.external-chat.store');

    // 求人
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');

    // 応募
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');

    // パイプライン
    Route::get('/pipeline', [App\Http\Controllers\PipelineController::class, 'index'])->name('pipeline.index');

    // 面接
    Route::get('/interviews', [App\Http\Controllers\InterviewController::class, 'index'])->name('interviews.index');

    // レポート
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    // マイページ
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');

    // 静的ページ（Bladeのまま）
    Route::view('/terms', 'static.terms')->name('terms');
    Route::view('/privacy', 'static.privacy')->name('privacy');

    // admin.phpは廃止（Inertia対応済みのルートをweb.phpで直接定義）
    // require __DIR__ . '/admin.php';
});

// 仮ルート（次回対応予定）
Route::middleware(['verify.jwt'])->group(function () {
    Route::get('/evaluations', function () {
        return \Inertia\Inertia::render('Dashboard', ['message' => '評価ページは準備中です']);
    })->name('evaluations.index');
    
    Route::get('/analytics', function () {
        return redirect()->route('dashboard');
    })->name('analytics.index');
});
