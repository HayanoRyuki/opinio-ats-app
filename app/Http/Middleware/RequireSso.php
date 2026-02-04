<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequireSso
{
    public function handle(Request $request, Closure $next)
    {
        // 開発環境バイパス
        if (app()->environment('local') && config('app.dev_bypass_auth', false)) {
            return $next($request);
        }

        // SSO callback は必ず素通り
        if ($request->is('sso/callback')) {
            return $next($request);
        }

        if (! $request->cookie('jwt')) {
            $authBase = rtrim(config('services.auth_app.url'), '/');

            $redirectUri = urlencode(
                url('/sso/callback')
            );

            $state = Str::random(32);

            return redirect()->away(
                "{$authBase}/sso/start"
                . "?client_id=ats"
                . "&redirect_uri={$redirectUri}"
                . "&state={$state}"
            );
        }

        return $next($request);
    }
}
