<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Interviewer\DashboardController as InterviewerDashboardController;

/*
|--------------------------------------------------------------------------
| Interviewer routes
|--------------------------------------------------------------------------
|
| ・SSO 開始は web.php の "/" のみ
| ・ここでは JWT と role のみを検証する
|
*/
Route::middleware([
    'verify.jwt',
    'role:interviewer',
])->prefix('interviewer')->group(function () {

    Route::get('/dashboard', [InterviewerDashboardController::class, 'index'])
        ->name('interviewer.dashboard');

});
