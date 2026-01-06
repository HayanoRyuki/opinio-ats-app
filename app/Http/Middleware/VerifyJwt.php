<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;
use App\Models\User;
use App\Models\Company;

final class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = null;

        // Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $jwt = substr($authHeader, 7);
        }

        // Cookie fallback
        if (! $jwt) {
            $jwt = $request->cookie('jwt');
        }

        if (! $jwt) {
            abort(401, 'JWT not provided');
        }

        try {
            $publicKey = file_get_contents(storage_path('oauth/public.key'));

            $payload = JWT::decode(
                $jwt,
                new Key($publicKey, 'RS256')
            );
        } catch (Throwable $e) {
            abort(401, 'Invalid JWT');
        }

        // aud チェック
        if (($payload->aud ?? null) !== config('app.url')) {
            abort(401, 'Invalid audience');
        }

        // user / company 解決（※最小構成）
        $user = User::find($payload->sub);
        if (! $user) {
            abort(401, 'User not found');
        }

        $company = Company::find($payload->company_id);
        if (! $company) {
            abort(401, 'Company not found');
        }

        // 🔽 Laravel Auth は使わない。Request に積むだけ
        $request->attributes->set('auth_user', $user);
        $request->attributes->set('role', $payload->role);
        $request->attributes->set('company', $company);

        return $next($request);
    }
}
