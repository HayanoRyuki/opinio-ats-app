<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyJwt;
use App\Http\Middleware\RequireSso;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Cookie Encryption
        |--------------------------------------------------------------------------
        | JWT は Auth App から渡されるため暗号化対象外
        */
        $middleware->encryptCookies(except: [
            'jwt',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Route Middleware Aliases
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            // JWT 検証（署名・exp・aud など）
            'verify.jwt'  => VerifyJwt::class,

            // JWT が無ければ Auth App にリダイレクト
            'require.sso' => RequireSso::class,

            // Role Gate（Auth 正史・Enum 解釈）
            'role'        => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
