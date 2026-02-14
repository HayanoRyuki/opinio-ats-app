<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Membership;

final class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | 0. 開発環境バイパス
        |--------------------------------------------------------------------------
        */
        if (app()->environment('local') && config('app.dev_bypass_auth', false)) {
            // 開発用に会社がなければ作成
            $company = \App\Models\Company::firstOrCreate(
                ['slug' => 'dev-company'],
                ['name' => 'Development Company']
            );

            // 開発用ユーザーをDBに作成（Gmail連携等のFK制約のため）
            $user = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Dev Admin',
                    'password' => bcrypt('password'),
                    'company_id' => $company->id,
                ]
            );
            $user->company_id = $company->id;
            $user->role = 'admin';
            Auth::setUser($user);
            $request->attributes->set('auth_user_id', $user->id);
            $request->attributes->set('company_id', $company->id);
            $request->attributes->set('role', $user->role);
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | 1. JWT 取得
        |--------------------------------------------------------------------------
        */
        $jwt = $request->cookie('jwt');

        if (! $jwt) {
            Log::warning('VerifyJwt: JWT missing');
            abort(401, 'JWT not provided');
        }

        /*
        |--------------------------------------------------------------------------
        | 2. JWT decode
        |--------------------------------------------------------------------------
        */
        try {
            $publicKey = file_get_contents(
                storage_path('oauth/public.key')
            );

            $payload = JWT::decode(
                $jwt,
                new Key($publicKey, 'RS256')
            );
        } catch (\Throwable $e) {
            Log::warning('VerifyJwt: Invalid JWT', [
                'error' => $e->getMessage(),
            ]);
            abort(401, 'Invalid JWT');
        }

        /*
        |--------------------------------------------------------------------------
        | 3. audience チェック
        |--------------------------------------------------------------------------
        */
        if (($payload->aud ?? null) !== 'ats.opinio.co.jp') {
            Log::warning('VerifyJwt: Invalid audience', [
                'aud' => $payload->aud ?? null,
            ]);
            abort(401, 'Invalid audience');
        }

        /*
        |--------------------------------------------------------------------------
        | 4. subject（user_id）チェック
        |--------------------------------------------------------------------------
        */
        $authUserId = (string) ($payload->sub ?? null);

        if (! $authUserId) {
            Log::warning('VerifyJwt: Invalid subject');
            abort(401, 'Invalid subject');
        }

        /*
        |--------------------------------------------------------------------------
        | 5. membership は「あれば使う」
        |--------------------------------------------------------------------------
        */
        $membership = Membership::where('user_id', $authUserId)->first();

        $role = $membership->role ?? ($payload->role ?? 'guest');
        $companyId = $membership->company_id ?? null;

        /*
        |--------------------------------------------------------------------------
        | 6. ATS 実効ユーザー構築（DBに永続化してFK制約を満たす）
        |--------------------------------------------------------------------------
        */
        $userName = $membership->name ?? ($payload->name ?? null);
        $userEmail = $membership->email ?? ($payload->email ?? null);

        // まずemailで既存ユーザーを検索、なければidで検索、なければ新規作成
        $user = null;
        if ($userEmail) {
            $user = User::where('email', $userEmail)->first();
        }
        if (!$user) {
            $user = User::find($authUserId);
        }
        if (!$user) {
            $user = User::create([
                'name' => $userName ?? 'Unknown',
                'email' => $userEmail ?? $authUserId . '@sso.opinio.co.jp',
                'password' => bcrypt(\Illuminate\Support\Str::random(32)),
                'company_id' => $companyId,
            ]);
        }

        // SSO側で名前・会社が変わった場合に同期
        $user->update(array_filter([
            'name' => $userName,
            'company_id' => $companyId,
            'role' => $role,
        ]));

        Auth::setUser($user);

        // request attributes
        $request->attributes->set('auth_user_id', $user->id);
        $request->attributes->set('company_id', $companyId);
        $request->attributes->set('role', $role);

        return $next($request);
    }
}
