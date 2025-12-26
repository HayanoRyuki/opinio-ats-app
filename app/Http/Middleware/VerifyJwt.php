<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        // ① Authorization ヘッダ → なければ session
        $authHeader = $request->header('Authorization');

        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $jwt = substr($authHeader, 7);
        } else {
            $jwt = session('jwt');
        }

        if (! $jwt) {
            abort(401);
        }

        try {
            $publicKeyPath = base_path('../../apps/auth-app/backend/storage/oauth/public.key');

            if (! file_exists($publicKeyPath)) {
                abort(500);
            }

            $publicKey = file_get_contents($publicKeyPath);

            $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));

            if (($decoded->aud ?? null) !== 'ats.opinio.co.jp') {
                abort(401);
            }

            $user = User::find((int) $decoded->sub);

            if (! $user) {
                abort(401);
            }

            Auth::login($user);

            $request->attributes->set('jwt', $decoded);
            $request->attributes->set('company_id', $decoded->company_id);
            $request->attributes->set('role', $decoded->role);

        } catch (Throwable $e) {
            abort(401);
        }

        return $next($request);
    }
}
