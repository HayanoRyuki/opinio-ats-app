<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use App\Enums\Role;

class SsoCallbackController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $code = $request->query('code');

        Log::info('ATS SSO callback received', [
            'code_present' => (bool) $code,
            'query'        => $request->query(),
        ]);

        if (! $code) {
            abort(401, 'missing_code');
        }

        // ① Auth サーバーから JWT 取得
        $response = Http::asForm()->post(
            config('services.auth.token_endpoint'),
            [
                'code'          => $code,
                'client_id'     => config('services.auth.client_id'),
                'client_secret' => config('services.auth.client_secret'),
            ]
        );

        if (! $response->successful()) {
            abort(401, 'token_request_failed');
        }

        $data = $response->json();

        if (! isset($data['access_token']['access_token'])) {
            abort(401, 'invalid_token_response');
        }

        $token = (string) $data['access_token']['access_token'];

        // ② JWT decode
        try {
            $decoded = JWT::decode(
                $token,
                new Key(
                    file_get_contents(storage_path('oauth/public.key')),
                    'RS256'
                )
            );
        } catch (\Throwable $e) {
            Log::error('JWT decode failed', ['error' => $e->getMessage()]);
            abort(401, 'invalid_jwt');
        }

        // ③ 必須クレーム確認
        if (! isset($decoded->sub, $decoded->email, $decoded->role)) {
            abort(403, 'invalid_jwt_payload');
        }

        $authUserId = (string) $decoded->sub;
        $email      = (string) $decoded->email;
        $roleValue  = (string) $decoded->role;

        // ④ ATS ユーザー確立（external_auth_user_id 正）
        $user = User::where('external_auth_user_id', $authUserId)->first();

        if (! $user) {
            // 既存 email ユーザー救済
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->external_auth_user_id = $authUserId;
            } else {
                $user = User::create([
                    'email'                  => $email,
                    'external_auth_user_id'  => $authUserId,
                ]);
            }
        }

        // ⑤ role 同期（Enum）
        $user->role = Role::from($roleValue);
        $user->save();

        // ⑥ ATS セッション確立
        Auth::login($user);

        // ⑦ role based redirect
        $redirectTo = match ($roleValue) {
            'admin', 'recruiter' => '/dashboard',
            'interviewer'        => '/interviewer/dashboard',
            default              => abort(403, 'invalid_role'),
        };

        return redirect($redirectTo)->withCookie(
            cookie(
                name: 'jwt',
                value: $token,
                minutes: 60 * 24,
                path: '/',
                domain: 'ats.opinio.co.jp',
                secure: true,
                httpOnly: true,
                sameSite: 'None'
            )
        );
    }
}
