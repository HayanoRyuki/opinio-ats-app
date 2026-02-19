<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\VerifyJwt;
use App\Http\Middleware\RequireSso;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ValidateIntakeApiKey;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

return Application::configure(basePath: dirname(__DIR__))

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    |
    | web.php     : 通常の ATS Web ルート（JWT 必須）
    | sso.php     : SSO callback 専用（JWT 不要・完全例外）
    | console.php : Artisan コマンド
    |
    */
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

        /*
         * ✅ SSO callback は「web を付けずに」読み込む
         *    → RequireSso / VerifyJwt を完全に回避
         */
        then: function () {
            Route::group(
                [],
                base_path('routes/sso.php')
            );
        },
    )

    /*
    |--------------------------------------------------------------------------
    | Global Middleware Configuration
    |--------------------------------------------------------------------------
    */
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | ❌ Laravel 標準 Auth を完全に無効化
        |--------------------------------------------------------------------------
        |
        | ATS は JWT / SSO 前提のため、標準 auth は使わない
        |
        */
        $middleware->remove([
            Authenticate::class,
            RedirectIfAuthenticated::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cookie Encryption
        |--------------------------------------------------------------------------
        |
        | JWT Cookie は暗号化しない（Auth サーバー発行前提）
        |
        */
        $middleware->encryptCookies(except: [
            'jwt',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Web Middleware Group
        |--------------------------------------------------------------------------
        */
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Route Middleware Aliases
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'verify.jwt'     => VerifyJwt::class,
            'require.sso'    => RequireSso::class,
            'role'           => CheckRole::class,
            'intake.apikey'  => ValidateIntakeApiKey::class,
        ]);
    })

    /*
    |--------------------------------------------------------------------------
    | Exception Handling
    |--------------------------------------------------------------------------
    */
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();
