<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root (always redirect to Auth SSO start)
|--------------------------------------------------------------------------
|
| 未ログインで ATS に直アクセスされた場合は、
| 必ず Auth アプリの SSO 開始画面へリダイレクトする
|
*/
Route::get('/', function () {
    $authAppUrl = 'https://auth.opinio.co.jp';
    $clientId   = 'ats';
    $callback   = config('app.url') . '/sso/callback';

    return redirect()->away(
        $authAppUrl
        . '/sso/start'
        . '?client_id=' . $clientId
        . '&redirect_uri=' . urlencode($callback)
    );
});

/*
|--------------------------------------------------------------------------
| Route groups
|--------------------------------------------------------------------------
*/
require __DIR__ . '/sso.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/interviewer.php';
