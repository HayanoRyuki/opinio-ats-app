<?php

namespace App\Services\Gmail;

use App\Enums\IntakeChannel;
use App\Enums\IntakeStatus;
use App\Models\ApplicationIntake;
use App\Models\GmailConnection;
use App\Models\IntakeCandidateDraft;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * GmailIntakeService - Gmail取得データのIntakeパイプライン登録
 *
 * GmailApiService で取得 → 各媒体パーサーで解析済みのデータを
 * 既存の ApplicationIntake → IntakeCandidateDraft パイプラインに登録する。
 *
 * 対応媒体：
 * - ビズリーチ（BizReachEmailParserService）
 * - ウォンテッドリー（WantedlyEmailParserService）
 */
class GmailIntakeService
{
    private GmailApiService $gmailApi;
    private BizReachEmailParserService $bizReachParser;
    private WantedlyEmailParserService $wantedlyParser;
    private DodaEmailParserService $dodaParser;
    private RikunabiEmailParserService $rikunabiParser;
    private MynaviEmailParserService $mynaviParser;

    public function __construct(
        GmailApiService $gmailApi,
        BizReachEmailParserService $bizReachParser,
        WantedlyEmailParserService $wantedlyParser,
        DodaEmailParserService $dodaParser,
        RikunabiEmailParserService $rikunabiParser,
        MynaviEmailParserService $mynaviParser
    ) {
        $this->gmailApi = $gmailApi;
        $this->bizReachParser = $bizReachParser;
        $this->wantedlyParser = $wantedlyParser;
        $this->dodaParser = $dodaParser;
        $this->rikunabiParser = $rikunabiParser;
        $this->mynaviParser = $mynaviParser;
    }

    /**
     * GmailConnection に対して同期処理を実行
     *
     * @return array{processed: int, created: int, skipped: int, errors: int}
     */
    public function syncConnection(GmailConnection $connection): array
    {
        $stats = [
            'processed' => 0,
            'created' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        try {
            $since = $connection->last_sync_at;

            // 全対応媒体のメールを一括取得
            $messageIds = $this->gmailApi->fetchAllPlatformEmailIds($connection, $since);

            Log::info('Gmail sync: fetched message IDs', [
                'connection_id' => $connection->id,
                'count' => count($messageIds),
            ]);

            foreach ($messageIds as $messageRef) {
                $stats['processed']++;

                try {
                    $result = $this->processMessage($connection, $messageRef['id']);

                    if ($result === 'created') {
                        $stats['created']++;
                    } elseif ($result === 'skipped') {
                        $stats['skipped']++;
                    }
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    Log::error('Gmail sync: message processing error', [
                        'connection_id' => $connection->id,
                        'message_id' => $messageRef['id'],
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            $connection->markSynced();

            Log::info('Gmail sync completed', [
                'connection_id' => $connection->id,
                'stats' => $stats,
            ]);

        } catch (\Throwable $e) {
            Log::error('Gmail sync: connection error', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $stats;
    }

    /**
     * 個別メッセージを処理
     *
     * @return string 'created' | 'skipped'
     */
    private function processMessage(GmailConnection $connection, string $messageId): string
    {
        // 1. 重複チェック
        $exists = ApplicationIntake::where('email_message_id', $messageId)->exists();
        if ($exists) {
            return 'skipped';
        }

        // 2. メール詳細を取得
        $emailData = $this->gmailApi->getMessageDetail($messageId);
        $from = $emailData['from'] ?? '';

        // 3. 媒体を判定してパース
        $platform = $this->detectPlatform($from);
        if ($platform === null) {
            return 'skipped';
        }

        $parsed = $this->parseByPlatform($platform, $emailData);

        // 4. Intake レコードを作成
        $this->createIntakeFromEmail($connection, $emailData, $parsed, $platform);

        return 'created';
    }

    /**
     * 送信元メールアドレスから媒体を判定
     */
    private function detectPlatform(string $from): ?string
    {
        if (BizReachEmailParserService::isBizReachEmail($from)) {
            return 'bizreach';
        }

        if (WantedlyEmailParserService::isWantedlyEmail($from)) {
            return 'wantedly';
        }

        if (DodaEmailParserService::isDodaEmail($from)) {
            return 'doda';
        }

        if (RikunabiEmailParserService::isRikunabiEmail($from)) {
            return 'rikunabi';
        }

        if (MynaviEmailParserService::isMynaviEmail($from)) {
            return 'mynavi';
        }

        return null;
    }

    /**
     * 媒体に応じたパーサーでメールを解析
     */
    private function parseByPlatform(string $platform, array $emailData): array
    {
        return match ($platform) {
            'bizreach' => $this->bizReachParser->parse($emailData),
            'wantedly' => $this->wantedlyParser->parse($emailData),
            'doda' => $this->dodaParser->parse($emailData),
            'rikunabi' => $this->rikunabiParser->parse($emailData),
            'mynavi' => $this->mynaviParser->parse($emailData),
            default => throw new \InvalidArgumentException("Unknown platform: {$platform}"),
        };
    }

    /**
     * 媒体名の日本語表示
     */
    private function getPlatformLabel(string $platform): string
    {
        return match ($platform) {
            'bizreach' => 'ビズリーチ',
            'wantedly' => 'Wantedly',
            'doda' => 'doda',
            'rikunabi' => 'リクナビ',
            'mynavi' => 'マイナビ',
            default => $platform,
        };
    }

    /**
     * メールデータから ApplicationIntake + IntakeCandidateDraft を作成
     */
    private function createIntakeFromEmail(
        GmailConnection $connection,
        array $emailData,
        array $parsedData,
        string $platform
    ): array {
        return DB::transaction(function () use ($connection, $emailData, $parsedData, $platform) {
            // ApplicationIntake 作成
            $intake = ApplicationIntake::create([
                'company_id' => $connection->company_id,
                'job_id' => null,
                'channel' => IntakeChannel::SCOUT,
                'source_id' => "gmail_{$platform}_" . $emailData['id'],
                'email_message_id' => $emailData['id'],
                'raw_data' => [
                    'gmail_message_id' => $emailData['id'],
                    'gmail_thread_id' => $emailData['threadId'],
                    'subject' => $emailData['subject'],
                    'from' => $emailData['from'],
                    'date' => $emailData['date'],
                    'body_text' => mb_substr($emailData['body_text'], 0, 5000),
                    'platform' => $platform,
                ],
                'parsed_data' => $parsedData,
                'status' => IntakeStatus::RECEIVED,
                'is_preliminary' => true,
                'received_at' => $this->parseEmailDate($emailData['date']),
            ]);

            // IntakeCandidateDraft 作成
            $platformUrl = $parsedData['bizreach_url']
                ?? $parsedData['wantedly_url']
                ?? $parsedData['doda_url']
                ?? $parsedData['rikunabi_url']
                ?? $parsedData['mynavi_url']
                ?? null;

            $draft = IntakeCandidateDraft::create([
                'application_intake_id' => $intake->id,
                'name' => $parsedData['candidate_name'] ?? '（氏名未取得）',
                'email' => $parsedData['candidate_email'] ?? null,
                'phone' => null,
                'is_preliminary' => true,
                'extracted_data' => [
                    'notification_type' => $parsedData['notification_type'],
                    'position' => $parsedData['position'] ?? null,
                    'message_summary' => $parsedData['message_summary'] ?? null,
                    'new_status' => $parsedData['new_status'] ?? null,
                    'platform_url' => $platformUrl,
                    'scout_service' => $this->getPlatformLabel($platform),
                    'platform' => $platform,
                    'response_type' => $this->mapNotificationToResponse($parsedData['notification_type']),
                    'email_subject' => $emailData['subject'],
                    'parse_success' => $parsedData['parse_success'],
                ],
            ]);

            $intake->update(['status' => IntakeStatus::PROCESSING]);

            Log::info('Gmail intake created', [
                'intake_id' => $intake->id,
                'draft_id' => $draft->id,
                'platform' => $platform,
                'candidate_name' => $parsedData['candidate_name'] ?? null,
                'notification_type' => $parsedData['notification_type'],
            ]);

            return [
                'intake_id' => $intake->id,
                'draft_id' => $draft->id,
            ];
        });
    }

    /**
     * メールの Date ヘッダーをパース
     */
    private function parseEmailDate(string $dateStr): \Carbon\Carbon
    {
        try {
            return \Carbon\Carbon::parse($dateStr);
        } catch (\Throwable) {
            return now();
        }
    }

    /**
     * 通知種別を response_type にマッピング
     */
    private function mapNotificationToResponse(string $notificationType): string
    {
        return match ($notificationType) {
            'application' => 'interested',
            'want_to_talk' => 'want_to_talk',
            'message' => 'want_to_talk',
            'status_change' => 'applied',
            'interview_schedule' => 'applied',
            'bookmark' => 'interested',
            default => 'interested',
        };
    }
}
