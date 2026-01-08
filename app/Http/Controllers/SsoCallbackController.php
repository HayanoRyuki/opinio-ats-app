<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

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

        // Auth サーバーから JWT 取得
        $response = Http::asForm()->post(
            config('services.auth.token_endpoint'),
            [
                'code'          => $code,
                'client_id'     => config('services.auth.client_id'),
                'client_secret' => config('services.auth.client_secret'),
            ]
        );

        if (! $response->successful()) {
            Log::error('Token request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            abort(401, 'token_request_failed');
        }

        $data = $response->json();

        if (! isset($data['access_token']['access_token'])) {
            Log::error('Invalid token response', ['response' => $data]);
            abort(401, 'invalid_token_response');
        }

        $token = (string) $data['access_token']['access_token'];

        // role に応じた初期遷移（最小限）
        // ※ 本当の認証・ユーザー解決は VerifyJwt に任せる
        return redirect('/dashboard')->withCookie(
            cookie(
                name: 'jwt',
                value: $token,
                minutes: 60 * 24,
                path: '/',
                domain: 'ats.opinio.co.jp',
                secure: true,
                httpOnly: true,
                sameSite: 'none'
            )
        );
    }
}
