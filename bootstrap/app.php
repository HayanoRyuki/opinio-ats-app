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
        | Disable Laravel session/auth/csrf (ATS is JWT-only)
        |--------------------------------------------------------------------------
        | Laravel 11 (Kernel-less) の既定 web stack に session/auth が積まれ、
        | Authenticate が 401 を返してしまうため、明示的に外す。
        */
        $middleware->remove([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);

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
