<?php

namespace App\Services\Gmail;

use Illuminate\Support\Facades\Log;

/**
 * WantedlyEmailParserService - Wantedly通知メール解析
 *
 * Wantedlyからの通知メールを解析し、
 * 候補者情報を構造化データとして抽出する。
 *
 * 対応する通知種別：
 * 1. 応募通知（application） - 募集への応募
 * 2. メッセージ通知（message） - メッセージ受信
 * 3. 話を聞きたい通知（want_to_talk） - 「話を聞きに行きたい」
 *
 * ※Wantedlyのメールフォーマットは実メールを見ながら逐次調整が必要。
 */
class WantedlyEmailParserService
{
    /**
     * 通知種別定数
     */
    public const TYPE_APPLICATION = 'application';     // 応募
    public const TYPE_MESSAGE = 'message';             // メッセージ受信
    public const TYPE_WANT_TO_TALK = 'want_to_talk';   // 話を聞きたい
    public const TYPE_UNKNOWN = 'unknown';              // 判別不能

    /**
     * メールをパースして構造化データを返す
     */
    public function parse(array $emailData): array
    {
        $result = [
            'notification_type' => self::TYPE_UNKNOWN,
            'candidate_name' => null,
            'candidate_email' => null,
            'position' => null,
            'message_summary' => null,
            'new_status' => null,
            'wantedly_url' => null,
            'parse_success' => false,
            'parse_errors' => [],
        ];

        try {
            $subject = $emailData['subject'] ?? '';
            $bodyHtml = $emailData['body_html'] ?? '';
            $bodyText = $emailData['body_text'] ?? '';

            // 1. 通知種別を件名から判定
            $result['notification_type'] = $this->detectNotificationType($subject);

            // 2. HTMLボディから情報を抽出（HTML優先、テキストフォールバック）
            $body = $bodyHtml ?: $bodyText;
            if (empty($body)) {
                $result['parse_errors'][] = 'メール本文が空です';
                return $result;
            }

            $isHtml = $bodyHtml !== '';

            // 3. 件名からも情報を抽出
            $this->parseSubject($subject, $result);

            // 4. 本文から情報を抽出
            if ($isHtml) {
                $this->parseHtmlBody($body, $result);
            } else {
                $this->parseTextBody($body, $result);
            }

            // 5. WantedlyのURLを抽出
            $result['wantedly_url'] = $this->extractWantedlyUrl($body);

            // 6. メッセージの場合は概要を抽出
            if ($result['notification_type'] === self::TYPE_MESSAGE) {
                $result['message_summary'] = $this->extractMessageSummary($body, $isHtml);
            }

            // パース成功判定（候補者名が取れていれば成功）
            $result['parse_success'] = !empty($result['candidate_name']);

        } catch (\Throwable $e) {
            $result['parse_errors'][] = $e->getMessage();
            Log::error('Wantedly email parse error', [
                'subject' => $emailData['subject'] ?? '',
                'error' => $e->getMessage(),
            ]);
        }

        return $result;
    }

    /**
     * 件名から通知種別を判定
     */
    private function detectNotificationType(string $subject): string
    {
        // 「話を聞きたい」系
        $wantToTalkPatterns = [
            '話を聞きに行きたい',
            '話を聞きたい',
            '興味があります',
            'に興味を持っています',
        ];

        foreach ($wantToTalkPatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_WANT_TO_TALK;
            }
        }

        // 応募系
        $applicationPatterns = [
            '応募',
            'エントリー',
            '候補者',
        ];

        foreach ($applicationPatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_APPLICATION;
            }
        }

        // メッセージ系
        $messagePatterns = [
            'メッセージ',
            '返信',
            'message',
        ];

        foreach ($messagePatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_MESSAGE;
            }
        }

        return self::TYPE_UNKNOWN;
    }

    /**
     * 件名から情報を抽出
     *
     * Wantedly件名の典型パターン：
     * - 「○○さんが「□□」に話を聞きに行きたいと言っています」
     * - 「○○さんが応募しました」
     * - 「○○さんからメッセージが届きました」
     */
    private function parseSubject(string $subject, array &$result): void
    {
        // パターン1: 「○○さんが「□□」に～」
        if (preg_match('/(.+?)さんが[「『](.+?)[」』]/u', $subject, $matches)) {
            $result['candidate_name'] = trim($matches[1]);
            $result['position'] = trim($matches[2]);
            return;
        }

        // パターン2: 「○○さんが応募しました」
        if (preg_match('/(.+?)さんが/u', $subject, $matches)) {
            $result['candidate_name'] = trim($matches[1]);
        }

        // パターン3: 「○○さんから」
        if (empty($result['candidate_name'])) {
            if (preg_match('/(.+?)さんから/u', $subject, $matches)) {
                $result['candidate_name'] = trim($matches[1]);
            }
        }
    }

    /**
     * HTMLメール本文から候補者情報を抽出
     */
    private function parseHtmlBody(string $html, array &$result): void
    {
        $text = $this->htmlToText($html);

        // 候補者名が件名から取得できていなければ本文から
        if (empty($result['candidate_name'])) {
            // パターン1: 「○○ さん」
            if (preg_match('/([^\s]{2,10})\s*さんが/u', $text, $matches)) {
                $result['candidate_name'] = trim($matches[1]);
            }
            // パターン2: 「名前：○○」
            if (empty($result['candidate_name'])) {
                if (preg_match('/(?:名前|氏名|候補者)[：:]\s*(.+?)(?:\s|$)/u', $text, $matches)) {
                    $result['candidate_name'] = trim($matches[1]);
                }
            }
        }

        // ポジション名が件名から取得できていなければ本文から
        if (empty($result['position'])) {
            // パターン1: 「募集：○○」
            if (preg_match('/(?:募集|ポジション|職種)[：:]\s*(.+?)(?:\n|$)/u', $text, $matches)) {
                $result['position'] = trim($matches[1]);
            }
            // パターン2: 「「○○」に」形式
            if (empty($result['position'])) {
                if (preg_match('/[「『](.+?)[」』](?:に|へ|の)/u', $text, $matches)) {
                    $result['position'] = trim($matches[1]);
                }
            }
        }

        // メールアドレスの抽出
        if (empty($result['candidate_email'])) {
            if (preg_match('/[\w.+-]+@[\w.-]+\.\w+/', $text, $matches)) {
                $email = $matches[0];
                if (!str_contains($email, 'wantedly') && !str_contains($email, 'example.com')) {
                    $result['candidate_email'] = $email;
                }
            }
        }
    }

    /**
     * テキストメール本文から候補者情報を抽出
     */
    private function parseTextBody(string $text, array &$result): void
    {
        $this->parseHtmlBody($text, $result);
    }

    /**
     * HTMLをプレーンテキストに変換
     */
    private function htmlToText(string $html): string
    {
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/(?:p|div|tr|td|li|h[1-6])>/i', "\n", $html);
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

    /**
     * メッセージ概要を抽出
     */
    private function extractMessageSummary(string $body, bool $isHtml): string
    {
        $text = $isHtml ? $this->htmlToText($body) : $body;

        if (preg_match('/(?:メッセージ(?:内容)?|本文)[：:]\s*\n?(.+?)(?:\n{2,}|---|\*{3}|$)/us', $text, $matches)) {
            $summary = trim($matches[1]);
            if (mb_strlen($summary) > 200) {
                $summary = mb_substr($summary, 0, 200) . '...';
            }
            return $summary;
        }

        return '';
    }

    /**
     * WantedlyのURLを抽出
     */
    private function extractWantedlyUrl(string $body): ?string
    {
        if (preg_match('/https?:\/\/(?:www\.)?wantedly\.com[^\s"\'<>]+/i', $body, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * このメールがWantedlyからの通知かどうかを判定
     */
    public static function isWantedlyEmail(string $from): bool
    {
        $wantedlyDomains = [
            '@wantedly.com',
            '@mail.wantedly.com',
            '@notification.wantedly.com',
            '@noreply.wantedly.com',
        ];

        foreach ($wantedlyDomains as $domain) {
            if (str_contains(strtolower($from), $domain)) {
                return true;
            }
        }

        return false;
    }
}
