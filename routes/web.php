<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\SharePipelineController;
use App\Models\Job;
use App\Http\Controllers\ShareApplicationController;

Route::get('/', function () {
    return redirect('/jobs');
});

Route::get('/jobs', [JobController::class, 'index'])
    ->name('jobs.index');

    Route::get('/jobs/{job}/pipeline', [PipelineController::class, 'show'])
    ->name('jobs.pipeline');

    Route::get('/__route_test', function () {
    return 'route ok';
});

Route::patch(
    '/applications/{application}/step',
    [\App\Http\Controllers\ApplicationStepController::class, 'update']
)->name('applications.step.update');


Route::get(
    '/share/jobs/{job}/{token}',
    [SharePipelineController::class, 'show']
)->name('jobs.pipeline.share');


Route::post('/jobs/{job}/share-token', function (Job $job) {
    $job->generateShareToken();
    return back();
})->name('jobs.share-token.generate');


Route::get(
    '/share/applications/{application}/{token}',
    [ShareApplicationController::class, 'show']
)->name('applications.share');