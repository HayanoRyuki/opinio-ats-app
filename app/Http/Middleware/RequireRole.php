<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;

class RequireRole
{
    /**
     * @param  string  ...$roles  Role::ADMIN->value など
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        // role が Enum に存在しない = 不正 JWT
        try {
            $role = Role::from($user->role);
        } catch (\ValueError $e) {
            abort(403, 'Invalid role');
        }

        if (! in_array($role->value, $roles, true)) {
            abort(403, 'Forbidden: insufficient role');
        }

        return $next($request);
    }
}
