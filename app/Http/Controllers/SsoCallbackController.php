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

        /**
         * Cookie を Response Header に直接セットする
         *（withCookie は使わない）
         */
        $response = redirect('/dashboard');

        $response->headers->setCookie(
            new Cookie(
                name: 'jwt',
                value: $token,
                expires: now()->addDay(),
                path: '/',
                domain: '.opinio.co.jp',
                secure: true,
                httpOnly: true,
                sameSite: 'None'
            )
        );

        return $response;
    }
}
