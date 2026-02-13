<?php

namespace App\Services\Gmail;

use Illuminate\Support\Facades\Log;

/**
 * RikunabiEmailParserService - リクナビNEXT/リクナビ通知メール解析
 *
 * リクナビからの通知メールを解析し、
 * 候補者情報を構造化データとして抽出する。
 *
 * 対応する通知種別：
 * 1. 応募通知（application）
 * 2. メッセージ通知（message）
 * 3. 気になる通知（bookmark） - 求人を気になるリストに追加
 */
class RikunabiEmailParserService
{
    public const TYPE_APPLICATION = 'application';
    public const TYPE_MESSAGE = 'message';
    public const TYPE_BOOKMARK = 'bookmark';
    public const TYPE_UNKNOWN = 'unknown';

    public function parse(array $emailData): array
    {
        $result = [
            'notification_type' => self::TYPE_UNKNOWN,
            'candidate_name' => null,
            'candidate_email' => null,
            'position' => null,
            'message_summary' => null,
            'new_status' => null,
            'rikunabi_url' => null,
            'parse_success' => false,
            'parse_errors' => [],
        ];

        try {
            $subject = $emailData['subject'] ?? '';
            $bodyHtml = $emailData['body_html'] ?? '';
            $bodyText = $emailData['body_text'] ?? '';

            $result['notification_type'] = $this->detectNotificationType($subject);

            $body = $bodyHtml ?: $bodyText;
            if (empty($body)) {
                $result['parse_errors'][] = 'メール本文が空です';
                return $result;
            }

            $isHtml = $bodyHtml !== '';
            $this->parseSubject($subject, $result);
            $this->parseBody($body, $isHtml, $result);
            $result['rikunabi_url'] = $this->extractRikunabiUrl($body);

            if ($result['notification_type'] === self::TYPE_MESSAGE) {
                $result['message_summary'] = $this->extractMessageSummary($body, $isHtml);
            }

            $result['parse_success'] = !empty($result['candidate_name']);

        } catch (\Throwable $e) {
            $result['parse_errors'][] = $e->getMessage();
            Log::error('Rikunabi email parse error', [
                'subject' => $emailData['subject'] ?? '',
                'error' => $e->getMessage(),
            ]);
        }

        return $result;
    }

    private function detectNotificationType(string $subject): string
    {
        $applicationPatterns = ['応募', 'エントリー'];
        foreach ($applicationPatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_APPLICATION;
            }
        }

        $bookmarkPatterns = ['気になる', 'お気に入り', 'ブックマーク'];
        foreach ($bookmarkPatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_BOOKMARK;
            }
        }

        $messagePatterns = ['メッセージ', '返信', '連絡'];
        foreach ($messagePatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_MESSAGE;
            }
        }

        return self::TYPE_UNKNOWN;
    }

    private function parseSubject(string $subject, array &$result): void
    {
        if (preg_match('/(.+?)様(?:が|より|から|の)/u', $subject, $matches)) {
            $result['candidate_name'] = trim($matches[1]);
        }

        if (preg_match('/[「『](.+?)[」』]/u', $subject, $matches)) {
            $result['position'] = trim($matches[1]);
        }
    }

    private function parseBody(string $body, bool $isHtml, array &$result): void
    {
        $text = $isHtml ? $this->htmlToText($body) : $body;

        if (empty($result['candidate_name'])) {
            if (preg_match('/(?:候補者名|氏名|名前|応募者名)[：:]\s*(.+?)(?:\s|$)/u', $text, $matches)) {
                $result['candidate_name'] = trim($matches[1]);
            }
        }

        if (empty($result['candidate_name'])) {
            if (preg_match('/([^\s]{1,10})\s*様(?:が|より|から|の)/u', $text, $matches)) {
                $result['candidate_name'] = trim($matches[1]);
            }
        }

        if (empty($result['position'])) {
            if (preg_match('/(?:求人|ポジション|職種|募集)[：:]\s*(.+?)(?:\n|$)/u', $text, $matches)) {
                $result['position'] = trim($matches[1]);
            }
        }

        if (empty($result['position'])) {
            if (preg_match('/[「『](.+?)[」』](?:に|へ|の)/u', $text, $matches)) {
                $result['position'] = trim($matches[1]);
            }
        }

        if (empty($result['candidate_email'])) {
            if (preg_match('/[\w.+-]+@[\w.-]+\.\w+/', $text, $matches)) {
                $email = $matches[0];
                if (!str_contains($email, 'rikunabi') && !str_contains($email, 'recruit') && !str_contains($email, 'example.com')) {
                    $result['candidate_email'] = $email;
                }
            }
        }
    }

    private function htmlToText(string $html): string
    {
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/(?:p|div|tr|td|li|h[1-6])>/i', "\n", $html);
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

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

    private function extractRikunabiUrl(string $body): ?string
    {
        // リクナビNEXT
        if (preg_match('/https?:\/\/(?:www\.)?next\.rikunabi\.com[^\s"\'<>]+/i', $body, $matches)) {
            return $matches[0];
        }
        // リクナビ（新卒向け等）
        if (preg_match('/https?:\/\/(?:www\.)?rikunabi\.com[^\s"\'<>]+/i', $body, $matches)) {
            return $matches[0];
        }

        return null;
    }

    public static function isRikunabiEmail(string $from): bool
    {
        $rikunabiDomains = [
            '@next.rikunabi.com',
            '@rikunabi.com',
            '@mail.rikunabi.com',
            '@r-agent.com',
            '@mail.r-agent.com',
        ];

        foreach ($rikunabiDomains as $domain) {
            if (str_contains(strtolower($from), $domain)) {
                return true;
            }
        }

        return false;
    }
}
