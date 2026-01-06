<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
         * DB は一切触らない
         * Auth を正史とし、JWT の中身だけを信頼する
         */
        $request->attributes->set(
            'auth_user_id',
            (string) $payload->sub
        );

        $request->attributes->set(
            'role',
            $payload->role ?? null
        );

        $request->attributes->set(
            'company_id',
            $payload->company_id ?? null
        );

        return $next($request);
    }
}
