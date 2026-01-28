<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Web\IntakeController;
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

    // 候補者
    Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])->name('candidates.show');

    // 求人
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');

    // 応募
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');

    // パイプライン（仮）
    Route::get('/pipeline', function () {
        return Inertia::render('Pipeline/Index');
    })->name('pipeline.index');

    // 面接（仮）
    Route::get('/interviews', function () {
        return Inertia::render('Interviews/Index');
    })->name('interviews.index');

    // レポート（仮）
    Route::get('/reports', function () {
        return Inertia::render('Reports/Index');
    })->name('reports.index');

    // 静的ページ（Bladeのまま）
    Route::view('/terms', 'static.terms')->name('terms');
    Route::view('/privacy', 'static.privacy')->name('privacy');

    require __DIR__ . '/admin.php';
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
