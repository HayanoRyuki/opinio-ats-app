<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\SsoCallbackController;

/*
|--------------------------------------------------------------------------
| JWT test (SSO)
|--------------------------------------------------------------------------
*/
Route::middleware(['require.sso','verify.jwt'])->get('/__jwt_test', function (Request $request) {
    return response()->json([
        'user'       => auth()->user(),
        'company_id' => $request->attributes->get('company_id'),
        'role'       => $request->attributes->get('role'),
        'jwt'        => $request->attributes->get('jwt'),
    ]);
});

/*
|--------------------------------------------------------------------------
| SSO callback
|--------------------------------------------------------------------------
*/
Route::get('/sso/callback', SsoCallbackController::class);
