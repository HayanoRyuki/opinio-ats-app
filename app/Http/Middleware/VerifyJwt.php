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
use App\Models\Company;

class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = null;

        // 1) Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $jwt = substr($authHeader, 7);
        }

        // 2) Cookie fallback
        if (! $jwt) {
            $jwt = $request->cookie('jwt');
        }

        // JWT が無い = SSO に戻す（401 は出さない）
        if (! $jwt) {
            return $this->redirectToSso();
        }

        try {
            $publicKey = file_get_contents(storage_path('oauth/public.key'));
            $payload   = JWT::decode($jwt, new Key($publicKey, 'RS256'));

            // company 解決（暫定）
            $company = Company::where('slug', 'opinio')->firstOrFail();

            // User 解決（Auth user_id = sub を external_auth_user_id として保存）
            $user = User::updateOrCreate(
                ['external_auth_user_id' => (string) $payload->sub],
                [
                    'name'       => 'auth_user_' . $payload->sub,
                    'email'      => 'auth_' . $payload->sub . '@opinio.local',
                    'company_id' => $company->id,
                    'password'   => bcrypt(str()->random(32)),
                ]
            );

            // role は JWT を正として runtime にのみ反映
            $user->setAttribute('role', $payload->role ?? null);

            Auth::setUser($user);

            $request->attributes->set('jwt', $payload);
            $request->attributes->set('company_id', $company->id);
            $request->attributes->set('role', $payload->role);
            $request->attributes->set('auth_user', $user);

        } catch (Throwable $e) {
            // JWT 不正・期限切れ → SSO に戻す
            return $this->redirectToSso();
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
