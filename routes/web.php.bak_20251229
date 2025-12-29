<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Middleware\VerifyJwt;
use App\Http\Middleware\RequireSso;

// テスト用 JWT + Cookie 保護ルート
Route::middleware(['require.sso','verify.jwt'])->get('/__jwt_test', function (Request $request) {
    return response()->json([
        'user' => auth()->user(),
        'company_id' => $request->attributes->get('company_id'),
        'role' => $request->attributes->get('role'),
        'jwt' => $request->attributes->get('jwt'),
    ]);
});
