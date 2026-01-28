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
| Admin / Recruiter routes
|--------------------------------------------------------------------------
|
| 前提：
| - verify.jwt は web.php 側ですでに適用済み
| - ここでは role のみを制御する
|
*/
Route::middleware([
    'role:admin,recruiter',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Applications
    |--------------------------------------------------------------------------
    */
    Route::get('/applications', [ApplicationController::class, 'index'])
        ->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])
        ->name('applications.show');

    /*
    |--------------------------------------------------------------------------
    | Candidates
    |--------------------------------------------------------------------------
    */
    Route::get('/candidates', [CandidateController::class, 'index'])
        ->name('candidates.index');
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])
        ->name('candidates.show');

    /*
    |--------------------------------------------------------------------------
    | Jobs
    |--------------------------------------------------------------------------
    */
    Route::get('/jobs', [JobController::class, 'index'])
        ->name('jobs.index');
    Route::get('/jobs/{job}', [JobController::class, 'show'])
        ->name('jobs.show');

    // （将来用：今は未実装でもOK）
    // Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    // Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');

    /*
    |--------------------------------------------------------------------------
    | Pipeline
    |--------------------------------------------------------------------------
    */
    Route::get('/pipeline', [PipelineController::class, 'index'])
        ->name('pipeline.index');
    Route::get('/pipeline/{pipeline}', [PipelineController::class, 'show'])
        ->name('pipeline.show');

    /*
    |--------------------------------------------------------------------------
    | Interviews
    |--------------------------------------------------------------------------
    */
    Route::get('/interviews', [InterviewController::class, 'index'])
        ->name('interviews.index');
    Route::get('/interviews/{interview}', [InterviewController::class, 'show'])
        ->name('interviews.show');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])
        ->name('reports.show');
});
