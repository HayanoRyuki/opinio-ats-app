<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireSso
{
    public function handle(Request $request, Closure $next)
    {
        /**
         * ============================
         * SSO callback は必ず素通り
         * ============================
         */
        if ($request->is('sso/callback')) {
            return $next($request);
        }

        /**
         * ============================
         * jwt が無い場合のみ Auth に飛ばす
         * ============================
         * ※ 正当性チェックは VerifyJwt が担当
         */
        if (! $request->hasCookie('jwt')) {
            return redirect()->away(
                rtrim(config('services.auth_app.url'), '/')
                . '/sso/start?client=ats'
            );
        }

        return $next($request);
    }
}
