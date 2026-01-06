<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyJwt;
use App\Http\Middleware\RequireSso;
use App\Http\Middleware\CheckRole;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | ❌ Laravel 標準 Auth を完全に無効化
        |--------------------------------------------------------------------------
        */
        $middleware->remove([
            Authenticate::class,
            RedirectIfAuthenticated::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cookie Encryption
        |--------------------------------------------------------------------------
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
            'verify.jwt'  => VerifyJwt::class,
            'require.sso' => RequireSso::class,
            'role'        => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
