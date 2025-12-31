<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
                'code'          => $code,
                'client_id'     => config('services.auth.client_id'),
                'client_secret' => config('services.auth.client_secret'),
            ]
        );

        Log::info('ATS token endpoint response', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if (! $response->successful()) {
            abort(401, 'token_request_failed');
        }

        $data = $response->json();

        if (! isset($data['access_token']['access_token'])) {
            abort(401, 'invalid_token_response');
        }

        $token = (string) $data['access_token']['access_token'];

        // JWT decode
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

        if (! isset($decoded->role)) {
            abort(403, 'role_missing');
        }

        // Role based redirect（Enumは使わない）
        $redirectTo = match ((string) $decoded->role) {
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
