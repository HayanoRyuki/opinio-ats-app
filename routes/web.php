<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root / Login (always redirect to Auth SSO start)
|--------------------------------------------------------------------------
|
| ATS はログイン画面を持たない。
| 直打ち・/login いずれも必ず Auth の SSO に飛ばす。
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
| Static pages (login required, role independent)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::view('/terms', 'static.terms')->name('terms');
    Route::view('/privacy', 'static.privacy')->name('privacy');
    Route::view('/data-policy', 'static.data-policy')->name('data-policy');
    Route::view('/ai-policy', 'static.ai-policy')->name('ai-policy');
    Route::view('/company', 'static.company')->name('company');
    Route::view('/help', 'static.help')->name('help');
});


/*
|--------------------------------------------------------------------------
| Route groups
|--------------------------------------------------------------------------
*/
require __DIR__ . '/sso.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/interviewer.php';
