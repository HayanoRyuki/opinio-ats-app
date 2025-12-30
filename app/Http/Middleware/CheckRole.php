<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * @param  mixed  ...$roles  許可する role（string）
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // 未認証（理論上は verify.jwt で弾かれるが念のため）
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        // role が無い = 不正状態
        if (! is_string($user->role)) {
            abort(403, 'Role not assigned');
        }

        // JWT の role を Enum に変換（存在しない role は即 NG）
        try {
            $role = Role::from($user->role);
        } catch (\ValueError $e) {
            abort(403, 'Invalid role');
        }

        // 許可された role か？
        if (! in_array($role->value, $roles, true)) {
            abort(403, 'Forbidden: insufficient role');
        }

        return $next($request);
    }
}
