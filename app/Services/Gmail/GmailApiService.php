<?php

namespace App\Services\Gmail;

use App\Models\GmailConnection;
use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * GmailApiService - Gmail API操作サービス
 *
 * Google_Client の初期化、トークンリフレッシュ、
 * メール検索・取得を担当する。
 */
class GmailApiService
{
    private GoogleClient $client;
    private Gmail $gmail;

    /**
     * GmailConnection からクライアントを初期化
     */
    public function initializeClient(GmailConnection $connection): void
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setAccessType('offline');

        $this->client->setAccessToken([
            'access_token' => $connection->access_token,
            'refresh_token' => $connection->refresh_token,
            'expires_in' => $connection->token_expires_at
                ? max(0, $connection->token_expires_at->diffInSeconds(now(), false))
                : 0,
        ]);

        // トークン期限切れの場合はリフレッシュ
        if ($this->client->isAccessTokenExpired()) {
            $this->refreshToken($connection);
        }

        $this->gmail = new Gmail($this->client);
    }

    /**
     * トークンをリフレッシュ
     */
    private function refreshToken(GmailConnection $connection): void
    {
        $this->client->fetchAccessTokenWithRefreshToken($connection->refresh_token);
        $token = $this->client->getAccessToken();

        if (isset($token['error'])) {
            Log::error('Gmail token refresh failed', [
                'connection_id' => $connection->id,
                'error' => $token['error'],
            ]);
            throw new \RuntimeException('Gmail token refresh failed: ' . ($token['error_description'] ?? $token['error']));
        }

        $connection->updateTokens(
            $token['access_token'],
            $token['refresh_token'] ?? null,
            $token['expires_in'] ?? 3600
        );

        Log::info('Gmail token refreshed', ['connection_id' => $connection->id]);
    }

    /**
     * 全対応媒体の通知メールを一括検索
     *
     * @param GmailConnection $connection
     * @param Carbon|null $since この日時以降のメールを取得（null の場合は lookback_days 日前）
     * @return array<array{id: string, threadId: string}>
     */
    public function fetchAllPlatformEmailIds(GmailConnection $connection, ?Carbon $since = null): array
    {
        $this->initializeClient($connection);

        $lookbackDays = config('services.gmail_sync.lookback_days', 7);
        $sinceTimestamp = $since
            ? $since->timestamp
            : now()->subDays($lookbackDays)->timestamp;

        // 全媒体のfromアドレスをOR結合
        $fromQueries = [
            // ビズリーチ
            'from:info@mail.bizreach.biz',
            'from:noreply@bizreach.biz',
            'from:notification@bizreach.biz',
            // ウォンテッドリー
            'from:noreply@wantedly.com',
            'from:notification@wantedly.com',
            'from:info@wantedly.com',
            // doda
            'from:*@doda.jp',
            'from:*@persol-career.co.jp',
            // リクナビ
            'from:*@rikunabi.com',
            'from:*@r-agent.com',
            // マイナビ
            'from:*@mynavi.jp',
            'from:*@mynavijob.jp',
        ];

        $query = '(' . implode(' OR ', $fromQueries) . ') ' . "after:{$sinceTimestamp}";

        return $this->fetchEmailIdsByQuery($connection, $query);
    }

    /**
     * ビズリーチ通知メールを検索・取得
     */
    public function fetchBizReachEmailIds(GmailConnection $connection, ?Carbon $since = null): array
    {
        $this->initializeClient($connection);

        $lookbackDays = config('services.gmail_sync.lookback_days', 7);
        $sinceTimestamp = $since
            ? $since->timestamp
            : now()->subDays($lookbackDays)->timestamp;

        $query = '(from:info@mail.bizreach.biz OR from:noreply@bizreach.biz OR from:notification@bizreach.biz) '
            . "after:{$sinceTimestamp}";

        return $this->fetchEmailIdsByQuery($connection, $query);
    }

    /**
     * Wantedly通知メールを検索・取得
     */
    public function fetchWantedlyEmailIds(GmailConnection $connection, ?Carbon $since = null): array
    {
        $this->initializeClient($connection);

        $lookbackDays = config('services.gmail_sync.lookback_days', 7);
        $sinceTimestamp = $since
            ? $since->timestamp
            : now()->subDays($lookbackDays)->timestamp;

        $query = '(from:noreply@wantedly.com OR from:notification@wantedly.com OR from:info@wantedly.com) '
            . "after:{$sinceTimestamp}";

        return $this->fetchEmailIdsByQuery($connection, $query);
    }

    /**
     * Gmailクエリでメールを検索（共通処理）
     */
    private function fetchEmailIdsByQuery(GmailConnection $connection, string $query): array
    {
        $messageIds = [];
        $pageToken = null;
        $batchSize = config('services.gmail_sync.batch_size', 50);

        do {
            $params = [
                'q' => $query,
                'maxResults' => min($batchSize, 100),
            ];

            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            try {
                $response = $this->gmail->users_messages->listUsersMessages('me', $params);

                $messages = $response->getMessages() ?? [];
                foreach ($messages as $message) {
                    $messageIds[] = [
                        'id' => $message->getId(),
                        'threadId' => $message->getThreadId(),
                    ];
                }

                $pageToken = $response->getNextPageToken();
            } catch (\Google\Service\Exception $e) {
                Log::error('Gmail API list error', [
                    'connection_id' => $connection->id,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);

                if ($e->getCode() === 429) {
                    sleep(2);
                    continue;
                }

                throw $e;
            }
        } while ($pageToken && count($messageIds) < $batchSize);

        return $messageIds;
    }

    /**
     * メールの詳細を取得
     *
     * @return array{
     *   id: string,
     *   threadId: string,
     *   subject: string,
     *   from: string,
     *   date: string,
     *   body_html: string,
     *   body_text: string,
     *   headers: array
     * }
     */
    public function getMessageDetail(string $messageId): array
    {
        try {
            $message = $this->gmail->users_messages->get('me', $messageId, [
                'format' => 'full',
            ]);

            return $this->parseMessage($message);
        } catch (\Google\Service\Exception $e) {
            Log::error('Gmail API get message error', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Gmail Message オブジェクトを構造化データに変換
     */
    private function parseMessage(Message $message): array
    {
        $headers = [];
        $payload = $message->getPayload();

        // ヘッダーを抽出
        foreach ($payload->getHeaders() as $header) {
            $name = strtolower($header->getName());
            $headers[$name] = $header->getValue();
        }

        // ボディを抽出（HTML/テキスト）
        $bodyHtml = '';
        $bodyText = '';
        $this->extractBody($payload, $bodyHtml, $bodyText);

        return [
            'id' => $message->getId(),
            'threadId' => $message->getThreadId(),
            'subject' => $headers['subject'] ?? '',
            'from' => $headers['from'] ?? '',
            'date' => $headers['date'] ?? '',
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
            'headers' => $headers,
        ];
    }

    /**
     * MIMEパートからHTML/テキストボディを再帰的に抽出
     */
    private function extractBody($part, string &$html, string &$text): void
    {
        $mimeType = $part->getMimeType();

        if ($mimeType === 'text/html' && $part->getBody()->getData()) {
            $html = $this->decodeBody($part->getBody()->getData());
        } elseif ($mimeType === 'text/plain' && $part->getBody()->getData()) {
            $text = $this->decodeBody($part->getBody()->getData());
        }

        // マルチパートの場合は子パートを再帰探索
        $parts = $part->getParts();
        if ($parts) {
            foreach ($parts as $childPart) {
                $this->extractBody($childPart, $html, $text);
            }
        }
    }

    /**
     * Base64url エンコードされたボディをデコード
     */
    private function decodeBody(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * OAuth 認証URLを生成
     */
    public static function getAuthUrl(): string
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect_uri'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->addScope(Gmail::GMAIL_READONLY);

        return $client->createAuthUrl();
    }

    /**
     * 認証コードからトークンを取得
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int, email: string}
     */
    public static function exchangeCode(string $code): array
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect_uri'));

        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \RuntimeException('OAuth token exchange failed: ' . ($token['error_description'] ?? $token['error']));
        }

        $client->setAccessToken($token);

        // Gmailアドレスを取得
        $gmail = new Gmail($client);
        $profile = $gmail->users->getProfile('me');

        return [
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? '',
            'expires_in' => $token['expires_in'] ?? 3600,
            'email' => $profile->getEmailAddress(),
        ];
    }
}
