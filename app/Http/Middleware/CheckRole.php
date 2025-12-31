<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // JWT が無い場合は role では判断しない
        // require.sso に処理を委ねる
        if (! $request->cookie('jwt')) {
            return $next($request);
        }

        // verify.jwt 後に入る想定
        $role = $request->attributes->get('role');

        if (! is_string($role)) {
            abort(403, 'Role not assigned');
        }

        if (! in_array($role, $roles, true)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
