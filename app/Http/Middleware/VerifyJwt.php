<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Membership;

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

        // auth の user_id（JWT sub）
        $authUserId = (string) ($payload->sub ?? null);

        if (! $authUserId) {
            abort(401, 'Invalid subject');
        }

        // Membership を1件取得（ATS 正史）
        $membership = Membership::where('user_id', $authUserId)->first();

        if (! $membership) {
            abort(403, 'No membership found');
        }

        /**
         * ============================
         * ATS 実効ユーザーを構築する
         * ============================
         */
        $user = new User();

        $user->id = $authUserId;
        $user->company_id = $membership->company_id;
        $user->role = $membership->role;

        // auth()->user() を有効化
        Auth::setUser($user);

        // request attributes（互換用）
        $request->attributes->set('auth_user_id', $authUserId);
        $request->attributes->set('company_id', $membership->company_id);
        $request->attributes->set('role', $membership->role);

        return $next($request);
    }
}
