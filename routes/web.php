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
| ※ SSO callback は「必ず絶対URLで固定」する
|
*/
Route::get('/', function () {
    return redirect()->away(
        'https://auth.opinio.co.jp/sso/start'
        . '?client_id=ats'
        . '&redirect_uri=' . urlencode('https://ats.opinio.co.jp/sso/callback')
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
