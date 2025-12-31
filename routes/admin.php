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
| Protected routes (admin / recruiter)
|--------------------------------------------------------------------------
|
| ・SSO 開始は web.php の "/" のみで行う
| ・ここでは JWT があることを前提に gate する
|
*/
Route::middleware([
    'verify.jwt',
    'role:admin,recruiter',
])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);

    Route::get('/candidates', [CandidateController::class, 'index']);
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show']);

    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{job}', [JobController::class, 'show']);

    Route::get('/pipeline', [PipelineController::class, 'index']);
    Route::get('/pipeline/{pipeline}', [PipelineController::class, 'show']);

    Route::get('/interviews', [InterviewController::class, 'index']);
    Route::get('/interviews/{interview}', [InterviewController::class, 'show']);

    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
});
