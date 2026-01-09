<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

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

        // 🔍 一時ログ（AUTH の生レスポンス確認用）
        Log::info('AUTH token endpoint response', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if (! $response->successful()) {
            Log::error('Token request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            abort(401, 'token_request_failed');
        }

        $data = $response->json();

        // AUTH 側のレスポンス差異に耐えるため両対応
        $token =
            $data['access_token']['access_token']
            ?? $data['access_token']
            ?? null;

        if (! $token) {
            Log::error('Invalid token response', ['response' => $data]);
            abort(401, 'invalid_token_response');
        }

        // Cookie をレスポンスヘッダに直接セット
        $response = redirect('/dashboard');

        $cookie = new Cookie(
            'jwt',                          // name
            $token,                         // value
            now()->addDay(),                // expires
            '/',                            // path
            '.opinio.co.jp',                // domain
            true,                           // secure
            true,                           // httpOnly
            false,                          // raw
            'None'                          // sameSite
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
