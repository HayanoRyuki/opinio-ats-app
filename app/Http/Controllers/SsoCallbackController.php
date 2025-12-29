<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SsoCallbackController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $jwt = $request->query('token');

        if (! $jwt) {
            abort(401);
        }

        return redirect('/')
            ->withCookie(
                cookie(
                    name: 'jwt',
                    value: $jwt,
                    minutes: 60 * 24,
                    path: '/',
                    domain: null,
                    secure: app()->environment('production'),
                    httpOnly: true,
                    raw: false,
                    sameSite: 'Lax'
                )
            );
    }
}
