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

        $tokenEndpoint = config('services.auth.token_endpoint');
        $clientId      = config('services.auth.client_id');
        $clientSecret  = config('services.auth.client_secret');

        // ★ Auth に登録している redirect_uri と完全一致させる
        $redirectUri = 'https://ats.opinio.co.jp/sso/callback';

        $response = Http::asForm()->post($tokenEndpoint, [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
        ]);

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

        $token = $data['access_token'] ?? null;

        if (! $token) {
            Log::error('Invalid token response', ['response' => $data]);
            abort(401, 'invalid_token_response');
        }

        $response = redirect('/');

        $cookie = new Cookie(
            'jwt',
            $token,
            now()->addDay(),
            '/',
            'ats.opinio.co.jp',
            true,
            true,
            false,
            'None'
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
