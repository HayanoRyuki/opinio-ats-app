<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireSso
{
    public function handle(Request $request, Closure $next)
    {
        // SSO callback 自体は必ず通す
        if ($request->is('sso/callback')) {
            return $next($request);
        }

        // JWT が無ければ Auth SSO へ
        if (! $request->cookie('jwt')) {
            $authBase = rtrim(config('services.auth_app.url'), '/');

            $redirectUri = urlencode('https://ats.opinio.co.jp/sso/callback');

            return redirect()->away(
                "{$authBase}/sso/start"
                . "?client_id=ats"
                . "&redirect_uri={$redirectUri}"
            );
        }

        return $next($request);
    }
}
