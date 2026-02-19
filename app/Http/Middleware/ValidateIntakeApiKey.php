<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * X-API-Key ヘッダーでAPIキー認証を行うミドルウェア。
 *
 * - APIキーが設定されていない場合（X-API-Keyヘッダーなし）:
 *   company_id がリクエストボディに含まれていれば通過（後方互換性）
 * - APIキーが設定されている場合:
 *   有効なキーであればリクエストにcompany_idを自動セット
 *
 * Chrome拡張・Gmail連携の両方から利用される。
 */
class ValidateIntakeApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKeyHeader = $request->header('X-API-Key');

        if ($apiKeyHeader) {
            // APIキーが提供された場合 → 検証
            $apiKey = ApiKey::findByPlainKey($apiKeyHeader);

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid or expired API key.',
                ], 401);
            }

            // 使用記録を更新
            $apiKey->update(['last_used_at' => now()]);

            // リクエストに会社IDをセット（ボディのcompany_idを上書き）
            $request->merge(['company_id' => $apiKey->company_id]);
            $request->attributes->set('api_key_id', $apiKey->id);
            $request->attributes->set('company_id', $apiKey->company_id);
        } else {
            // APIキーなし → company_id がボディに必要（後方互換）
            if (!$request->has('company_id')) {
                return response()->json([
                    'success' => false,
                    'error' => 'API key (X-API-Key header) or company_id is required.',
                ], 401);
            }
        }

        return $next($request);
    }
}
