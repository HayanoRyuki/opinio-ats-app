<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // verify.jwt で検証済み前提。
        // Auth App 由来の role は request attributes に入っている
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
