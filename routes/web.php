<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PipelineController;

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