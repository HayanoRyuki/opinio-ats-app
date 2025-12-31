<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root (SSO entrypoint)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    // JWT が無ければ Auth に飛ばす
    if (! request()->cookie('jwt')) {
        $authApp = config('services.auth_app.url', 'https://auth.opinio.co.jp');
        return redirect()->away($authApp . '/sso/start?client=ats');
    }

    // JWT があれば dashboard へ
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| Route groups
|--------------------------------------------------------------------------
*/
require __DIR__.'/sso.php';
require __DIR__.'/admin.php';
require __DIR__.'/interviewer.php';
