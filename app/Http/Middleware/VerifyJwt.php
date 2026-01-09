<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Membership;

final class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | 1. JWT 取得
        |--------------------------------------------------------------------------
        */
        $jwt = $request->cookie('jwt');

        if (! $jwt) {
            Log::warning('VerifyJwt: JWT missing');
            abort(401, 'JWT not provided');
        }

        /*
        |--------------------------------------------------------------------------
        | 2. JWT decode
        |--------------------------------------------------------------------------
        */
        try {
            $publicKey = file_get_contents(
                storage_path('oauth/public.key')
            );

            $payload = JWT::decode(
                $jwt,
                new Key($publicKey, 'RS256')
            );
        } catch (\Throwable $e) {
            Log::warning('VerifyJwt: Invalid JWT', [
                'error' => $e->getMessage(),
            ]);
            abort(401, 'Invalid JWT');
        }

        Log::info('VerifyJwt: JWT decoded', [
            'payload' => (array) $payload,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. audience チェック
        |--------------------------------------------------------------------------
        */
        if (($payload->aud ?? null) !== 'ats.opinio.co.jp') {
            Log::warning('VerifyJwt: Invalid audience', [
                'aud' => $payload->aud ?? null,
            ]);
            abort(401, 'Invalid audience');
        }

        /*
        |--------------------------------------------------------------------------
        | 4. subject（user_id）チェック
        |--------------------------------------------------------------------------
        */
        $authUserId = (string) ($payload->sub ?? null);

        if (! $authUserId) {
            Log::warning('VerifyJwt: Invalid subject', [
                'payload' => (array) $payload,
            ]);
            abort(401, 'Invalid subject');
        }

        Log::info('VerifyJwt: Subject OK', [
            'user_id' => $authUserId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. Membership 取得
        |--------------------------------------------------------------------------
        */
        Log::info('VerifyJwt: Looking up membership', [
            'user_id' => $authUserId,
        ]);

        $membership = Membership::where('user_id', $authUserId)->first();

        if (! $membership) {
            Log::warning('VerifyJwt: No membership found', [
                'user_id' => $authUserId,
            ]);
            abort(403, 'No membership found');
        }

        Log::info('VerifyJwt: Membership found', [
            'company_id' => $membership->company_id,
            'role'       => $membership->role,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6. ATS 実効ユーザー構築
        |--------------------------------------------------------------------------
        */
        $user = new User();
        $user->id         = $authUserId;
        $user->company_id = $membership->company_id;
        $user->role       = $membership->role;

        Auth::setUser($user);

        // request attributes（互換用）
        $request->attributes->set('auth_user_id', $authUserId);
        $request->attributes->set('company_id', $membership->company_id);
        $request->attributes->set('role', $membership->role);

        return $next($request);
    }
}
