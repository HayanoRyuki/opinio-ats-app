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

        $response = Http::asForm()->post(
            config('services.auth.token_endpoint'),
            [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'client_id'     => config('services.auth.client_id'),
                'client_secret' => config('services.auth.client_secret'),
                'redirect_uri'  => 'https://ats.opinio.co.jp/sso/callback',
            ]
        );

        Log::info('AUTH token endpoint response', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if (! $response->successful()) {
            abort(401, 'token_request_failed');
        }

        $token = $response->json('access_token');

        if (! $token) {
            abort(401, 'invalid_token_response');
        }

        $response = redirect('/');

        // ✅ expires は DateTime で渡す
        $cookie = Cookie::create(
            'jwt',
            $token,
            now()->addDay(), // ← ここが超重要
            '/',
            null,
            true,
            true,
            false,
            'None'
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
