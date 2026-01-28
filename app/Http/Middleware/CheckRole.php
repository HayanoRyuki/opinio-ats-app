<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // verify.jwt が成功した場合のみ role が attributes に入る
        // role が無い場合は、ここでは判断しない
        if (! $request->attributes->has('role')) {
            return $next($request);
        }

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
