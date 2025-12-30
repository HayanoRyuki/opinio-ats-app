<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    DashboardController,
    ApplicationController,
    CandidateController,
    JobController,
    PipelineController,
    InterviewController,
    ReportController,
    SsoCallbackController
};

/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| JWT test (SSO)
|--------------------------------------------------------------------------
*/
Route::middleware(['require.sso','verify.jwt'])->get('/__jwt_test', function (Request $request) {
    return response()->json([
        'user' => auth()->user(),
        'company_id' => $request->attributes->get('company_id'),
        'role' => $request->attributes->get('role'),
        'jwt' => $request->attributes->get('jwt'),
    ]);
});

/*
|--------------------------------------------------------------------------
| SSO callback
|--------------------------------------------------------------------------
*/
Route::get('/sso/callback', SsoCallbackController::class);

/*
|--------------------------------------------------------------------------
| Protected routes
|--------------------------------------------------------------------------
*/
Route::middleware(['require.sso','verify.jwt'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);

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
