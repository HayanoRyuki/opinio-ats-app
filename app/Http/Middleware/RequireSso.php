<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireSso
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->cookie('jwt')) {
            $authApp = config('services.auth_app.url', 'http://localhost:8000');

            return redirect()->away(
                $authApp . '/sso/start?client=ats'
            );
        }

        return $next($request);
    }
}
