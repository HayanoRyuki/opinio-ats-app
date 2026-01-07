<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

final class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        // JWT は Cookie のみを正とする（SSO 前提）
        $jwt = $request->cookie('jwt');

        if (! $jwt) {
            abort(401, 'JWT not provided');
        }

        try {
            $publicKey = file_get_contents(
                storage_path('oauth/public.key')
            );

            $payload = JWT::decode(
                $jwt,
                new Key($publicKey, 'RS256')
            );
        } catch (\Throwable) {
            abort(401, 'Invalid JWT');
        }

        // aud は固定文字列で厳密チェック
        if (($payload->aud ?? null) !== 'ats.opinio.co.jp') {
            abort(401, 'Invalid audience');
        }

        /**
         * ============================
         * Auth コンテキストを構築する
         * ============================
         *
         * DB には触らない
         * JWT を正史として「仮想 User」を作る
         */
        $user = new User();

        $user->id = (string) $payload->sub;
        $user->role = $payload->role ?? null;
        $user->company_id = $payload->company_id ?? null;
        $user->email = $payload->email ?? null;

        // ★ これがないと auth()->user() は null のまま
        Auth::setUser($user);

        /**
         * request attributes は「補助情報」として保持
         * （既存コードを壊さない）
         */
        $request->attributes->set('auth_user_id', (string) $payload->sub);
        $request->attributes->set('role', $payload->role ?? null);
        $request->attributes->set('company_id', $payload->company_id ?? null);

        return $next($request);
    }
}
