<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatSummaryService
{
    /**
     * 外部チャット履歴をClaude APIで要約する
     */
    public function summarize(string $rawText, string $candidateName, string $source): string
    {
        $apiKey = config('services.anthropic.api_key');

        if (! $apiKey) {
            Log::warning('ChatSummaryService: ANTHROPIC_API_KEY not set');
            return 'AI要約は利用できません（APIキー未設定）';
        }

        $sourceLabel = match ($source) {
            'bizreach' => 'ビズリーチ',
            'wantedly' => 'Wantedly',
            default => '外部サービス',
        };

        $prompt = <<<PROMPT
以下は「{$candidateName}」との{$sourceLabel}でのチャット履歴です。

この会話の内容を分析し、以下の観点で簡潔にまとめてください：
- 現在の選考状況（どの段階にいるか）
- 候補者の温度感・反応
- 次のアクションとして何が必要か

---
{$rawText}
---

日本語で、箇条書きで簡潔にまとめてください。
PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? '要約の取得に失敗しました';
            }

            Log::error('ChatSummaryService: API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return '要約の生成に失敗しました（APIエラー）';
        } catch (\Throwable $e) {
            Log::error('ChatSummaryService: Exception', [
                'message' => $e->getMessage(),
            ]);

            return '要約の生成に失敗しました（通信エラー）';
        }
    }
}
