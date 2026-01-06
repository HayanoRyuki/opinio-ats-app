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
use App\Models\Company;
use Throwable;

class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = null;

        /*
        |--------------------------------------------------------------------------
        | 1. JWT 取得（Header → Cookie）
        |--------------------------------------------------------------------------
        */
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $jwt = substr($authHeader, 7);
        }

        if (! $jwt) {
            $jwt = $request->cookie('jwt');
        }

        /*
        |--------------------------------------------------------------------------
        | 2. JWT 無し → Auth SSO へ
        |--------------------------------------------------------------------------
        */
        if (! $jwt) {
            return $this->redirectToSso();
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | 3. JWT decode
            |--------------------------------------------------------------------------
            */
            $publicKey = file_get_contents(storage_path('oauth/public.key'));
            $payload   = JWT::decode($jwt, new Key($publicKey, 'RS256'));

            // ★ 重要：JWT の中身を必ずログに出す（今回の目的）
            Log::info('ATS JWT payload', (array) $payload);

            /*
            |--------------------------------------------------------------------------
            | 4. Company 解決（暫定：slug 固定）
            |--------------------------------------------------------------------------
            */
            $company = Company::where('slug', 'opinio')->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | 5. User 解決（Auth 側 sub を外部IDとして同期）
            |--------------------------------------------------------------------------
            */
            $user = User::updateOrCreate(
                ['external_auth_user_id' => (string) $payload->sub],
                [
                    'name'       => 'auth_user_' . $payload->sub,
                    'email'      => 'auth_' . $payload->sub . '@opinio.local',
                    'company_id' => $company->id,
                    'password'   => bcrypt(str()->random(32)),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 6. role は DB 保存せず、JWT を正として runtime 反映
            |--------------------------------------------------------------------------
            */
            $user->setAttribute('role', $payload->role ?? null);

            Auth::setUser($user);

            /*
            |--------------------------------------------------------------------------
            | 7. request に詰める（Controller / Policy 用）
            |--------------------------------------------------------------------------
            */
            $request->attributes->set('jwt', $payload);
            $request->attributes->set('company_id', $company->id);
            $request->attributes->set('role', $payload->role ?? null);
            $request->attributes->set('auth_user', $user);

        } catch (Throwable $e) {
            /*
            |--------------------------------------------------------------------------
            | 8. 失敗時ログ（原因確定用）
            |--------------------------------------------------------------------------
            */
            Log::error('VerifyJwt failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            abort(403, 'verify_jwt_failed');
        }

        return $next($request);
    }

    private function redirectToSso(): Response
    {
        return redirect()->away(
            'https://auth.opinio.co.jp/sso/start'
            . '?client_id=ats'
            . '&redirect_uri=' . urlencode('https://ats.opinio.co.jp/sso/callback')
        );
    }
}
