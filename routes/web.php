<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Root / Login
|--------------------------------------------------------------------------
|
| ATS はログイン画面を持たない。
| / および /login は常に Auth SSO にリダイレクトする。
|
*/
$redirectToAuth = function () {
    return redirect()->away(
        'https://auth.opinio.co.jp/sso/start'
        . '?client_id=ats'
        . '&redirect_uri=' . urlencode('https://ats.opinio.co.jp/sso/callback')
    );
};

Route::get('/', $redirectToAuth);
Route::get('/login', $redirectToAuth);

/*
|--------------------------------------------------------------------------
| Public routes (JWT 不要)
|--------------------------------------------------------------------------
|
| SSO callback / JWT test など
|
*/
require __DIR__ . '/sso.php';

/*
|--------------------------------------------------------------------------
| Protected routes (JWT 必須)
|--------------------------------------------------------------------------
|
| ATS の実画面はすべて verify.jwt を通す
|
*/
Route::middleware(['verify.jwt'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Static pages (role independent)
    |--------------------------------------------------------------------------
    */
    Route::view('/terms', 'static.terms')->name('terms');
    Route::view('/privacy', 'static.privacy')->name('privacy');
    Route::view('/data-policy', 'static.data-policy')->name('data-policy');
    Route::view('/ai-policy', 'static.ai-policy')->name('ai-policy');
    Route::view('/company', 'static.company')->name('company');
    Route::view('/help', 'static.help')->name('help');

    /*
    |--------------------------------------------------------------------------
    | Dashboard (正式)
    |--------------------------------------------------------------------------
    |
    | 採用活動の全体状況と、今日やるべきことを確認する入口。
    | Controller で集計し、View は表示専用。
    |
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Role based route groups
    |--------------------------------------------------------------------------
    */
    require __DIR__ . '/admin.php';
    require __DIR__ . '/interviewer.php';
});
