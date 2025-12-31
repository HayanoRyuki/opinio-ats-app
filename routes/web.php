<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root (always redirect to Auth)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $authApp = config('services.auth_app.url', 'https://auth.opinio.co.jp');
    return redirect()->away($authApp . '/sso/start?client=ats');
});

/*
|--------------------------------------------------------------------------
| Route groups
|--------------------------------------------------------------------------
*/
require __DIR__.'/sso.php';
require __DIR__.'/admin.php';
require __DIR__.'/interviewer.php';
