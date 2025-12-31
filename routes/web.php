<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root (always redirect to Auth)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->away('https://auth.opinio.co.jp/sso/start?client=ats');
});

/*
|--------------------------------------------------------------------------
| Route groups
|--------------------------------------------------------------------------
*/
require __DIR__.'/sso.php';
require __DIR__.'/admin.php';
require __DIR__.'/interviewer.php';
