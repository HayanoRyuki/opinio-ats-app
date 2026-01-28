<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Interviewer\DashboardController as InterviewerDashboardController;

/*
|--------------------------------------------------------------------------
| Interviewer routes
|--------------------------------------------------------------------------
*/
Route::middleware(['role:interviewer'])
    ->prefix('interviewer')
    ->group(function () {

        Route::get('/dashboard', [InterviewerDashboardController::class, 'index'])
            ->name('interviewer.dashboard');
    });
