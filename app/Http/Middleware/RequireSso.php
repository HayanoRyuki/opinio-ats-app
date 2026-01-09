<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireSso
{
    public function handle(Request $request, Closure $next)
    {
        /*
        |--------------------------------------------------------------------------
        | ✅ SSO / 例外ルートは必ず素通り
        |--------------------------------------------------------------------------
        |
        | - SSO callback（Cookie をセットする唯一の入口）
        | - JWT 動作確認用
        | - ヘルスチェック
        |
        */
        if (
            $request->is('sso/*') ||
            $request->is('__jwt_test') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | 🔐 JWT がなければ Auth App へ
        |--------------------------------------------------------------------------
        */
        if (! $request->hasCookie('jwt')) {
            return redirect()->away(
                rtrim(config('services.auth_app.url'), '/') . '/sso/start?client=ats'
            );
        }

        return $next($request);
    }
}
