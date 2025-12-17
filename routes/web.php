<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

Route::get('/', function () {
    return redirect('/jobs');
});

Route::get('/jobs', [JobController::class, 'index'])
    ->name('jobs.index');
