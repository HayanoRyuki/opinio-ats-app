<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireSso
{
    public function handle(Request $request, Closure $next)
    {
        // ★ SSO callback は必ず素通りさせる
        if ($request->is('sso/callback')) {
            return $next($request);
        }

        if (! $request->cookie('jwt')) {
            $authApp = config('services.auth_app.url');

            return redirect()->away(
                rtrim($authApp, '/') . '/sso/start?client=ats'
            );
        }

        return $next($request);
    }
}
