<?php

namespace App\Services\Gmail;

use Illuminate\Support\Facades\Log;

/**
 * BizReachEmailParserService - ビズリーチ通知メール解析
 *
 * ビズリーチからの通知メールを解析し、
 * 候補者情報を構造化データとして抽出する。
 *
 * 対応する通知種別：
 * 1. 応募通知 / 興味あり通知（application）
 * 2. メッセージ通知（message）
 * 3. 選考ステータス変更（status_change）
 *
 * ※ビズリーチのメールフォーマットは実メールを見ながら逐次調整が必要。
 *   以下は一般的なパターンに基づく骨格実装。
 */
class BizReachEmailParserService
{
    /**
     * 通知種別定数
     */
    public const TYPE_APPLICATION = 'application';     // 応募・興味あり
    public const TYPE_MESSAGE = 'message';             // メッセージ受信
    public const TYPE_STATUS_CHANGE = 'status_change'; // 選考ステータス変更
    public const TYPE_UNKNOWN = 'unknown';             // 判別不能

    /**
     * メールをパースして構造化データを返す
     *
     * @param array $emailData GmailApiService::getMessageDetail() の戻り値
     * @return array{
     *   notification_type: string,
     *   candidate_name: ?string,
     *   candidate_email: ?string,
     *   position: ?string,
     *   message_summary: ?string,
     *   new_status: ?string,
     *   bizreach_url: ?string,
     *   parse_success: bool,
     *   parse_errors: array
     * }
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
            'bizreach_url' => null,
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

            // 3. 種別に応じた抽出処理
            switch ($result['notification_type']) {
                case self::TYPE_APPLICATION:
                    $this->parseApplicationEmail($body, $bodyHtml !== '', $result);
                    break;
                case self::TYPE_MESSAGE:
                    $this->parseMessageEmail($body, $bodyHtml !== '', $result);
                    break;
                case self::TYPE_STATUS_CHANGE:
                    $this->parseStatusChangeEmail($body, $bodyHtml !== '', $result);
                    break;
                default:
                    // unknown でも基本抽出は試みる
                    $this->parseGenericEmail($body, $bodyHtml !== '', $result);
                    break;
            }

            // 4. ビズリーチURLを抽出
            $result['bizreach_url'] = $this->extractBizReachUrl($body);

            // パース成功判定（候補者名が取れていれば成功）
            $result['parse_success'] = !empty($result['candidate_name']);

        } catch (\Throwable $e) {
            $result['parse_errors'][] = $e->getMessage();
            Log::error('BizReach email parse error', [
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
        // 応募・興味あり系
        $applicationPatterns = [
            '応募',
            '興味あり',
            'スカウトに返信',
            '返信がありました',
            '気になる',
            'プレミアムスカウト',
            '求人に応募',
        ];

        foreach ($applicationPatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_APPLICATION;
            }
        }

        // メッセージ系
        $messagePatterns = [
            'メッセージ',
            'メールを受信',
            '新着メール',
            '返信',
        ];

        foreach ($messagePatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_MESSAGE;
            }
        }

        // 選考ステータス変更系
        $statusPatterns = [
            '選考',
            'ステータス',
            '不合格',
            '合格',
            '内定',
            '辞退',
        ];

        foreach ($statusPatterns as $pattern) {
            if (mb_strpos($subject, $pattern) !== false) {
                return self::TYPE_STATUS_CHANGE;
            }
        }

        return self::TYPE_UNKNOWN;
    }

    /**
     * 応募通知メールをパース
     */
    private function parseApplicationEmail(string $body, bool $isHtml, array &$result): void
    {
        if ($isHtml) {
            $this->parseHtmlBody($body, $result);
        } else {
            $this->parseTextBody($body, $result);
        }
    }

    /**
     * メッセージ通知メールをパース
     */
    private function parseMessageEmail(string $body, bool $isHtml, array &$result): void
    {
        if ($isHtml) {
            $this->parseHtmlBody($body, $result);
        } else {
            $this->parseTextBody($body, $result);
        }

        // メッセージ概要の抽出を試みる
        $result['message_summary'] = $this->extractMessageSummary($body, $isHtml);
    }

    /**
     * 選考ステータス変更メールをパース
     */
    private function parseStatusChangeEmail(string $body, bool $isHtml, array &$result): void
    {
        if ($isHtml) {
            $this->parseHtmlBody($body, $result);
        } else {
            $this->parseTextBody($body, $result);
        }

        // ステータス抽出
        $result['new_status'] = $this->extractStatus($body);
    }

    /**
     * 汎用パース（種別不明の場合）
     */
    private function parseGenericEmail(string $body, bool $isHtml, array &$result): void
    {
        if ($isHtml) {
            $this->parseHtmlBody($body, $result);
        } else {
            $this->parseTextBody($body, $result);
        }
    }

    /**
     * HTMLメール本文から候補者情報を抽出
     *
     * ビズリーチのHTMLメールは一般的に以下の構造:
     * - テーブルレイアウトで候補者名、ポジション名等が記載
     * - 「詳細を確認する」リンクが含まれる
     */
    private function parseHtmlBody(string $html, array &$result): void
    {
        // HTMLタグを除去してテキスト化（構造を保持しつつ）
        $text = $this->htmlToText($html);

        // 候補者名の抽出パターン
        // パターン1: 「○○ 様」形式
        if (preg_match('/([^\s]{1,10})\s*様(?:が|より|から|の)/u', $text, $matches)) {
            $result['candidate_name'] = trim($matches[1]);
        }

        // パターン2: 「候補者名：○○」形式
        if (empty($result['candidate_name'])) {
            if (preg_match('/(?:候補者名|氏名|名前)[：:]\s*(.+?)(?:\s|$)/u', $text, $matches)) {
                $result['candidate_name'] = trim($matches[1]);
            }
        }

        // パターン3: 「○○さん」形式（Wantedly風だがビズリーチでも使用される場合）
        if (empty($result['candidate_name'])) {
            if (preg_match('/([^\s]{2,10})さんが/u', $text, $matches)) {
                $result['candidate_name'] = trim($matches[1]);
            }
        }

        // ポジション名の抽出
        if (preg_match('/(?:求人|ポジション|職種)[：:]\s*(.+?)(?:\n|$)/u', $text, $matches)) {
            $result['position'] = trim($matches[1]);
        }

        // 求人タイトルからポジション抽出（「「○○」に応募がありました」形式）
        if (empty($result['position'])) {
            if (preg_match('/[「『](.+?)[」』](?:に|へ|の)/u', $text, $matches)) {
                $result['position'] = trim($matches[1]);
            }
        }

        // メールアドレスの抽出（まれに含まれる場合）
        if (preg_match('/[\w.+-]+@[\w.-]+\.\w+/', $text, $matches)) {
            // ビズリーチ自身のアドレスは除外
            $email = $matches[0];
            if (!str_contains($email, 'bizreach') && !str_contains($email, 'example.com')) {
                $result['candidate_email'] = $email;
            }
        }
    }

    /**
     * テキストメール本文から候補者情報を抽出
     */
    private function parseTextBody(string $text, array &$result): void
    {
        // HTMLパースと同じロジック（テキストはそのまま使える）
        $this->parseHtmlBody($text, $result);
    }

    /**
     * HTMLをプレーンテキストに変換（改行を保持）
     */
    private function htmlToText(string $html): string
    {
        // <br>, <p>, <div>, <tr>, <td> の前に改行を挿入
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/(?:p|div|tr|td|li|h[1-6])>/i', "\n", $html);

        // HTMLタグを除去
        $text = strip_tags($html);

        // HTMLエンティティをデコード
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 連続する空行を削除
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * メッセージ概要を抽出
     */
    private function extractMessageSummary(string $body, bool $isHtml): string
    {
        $text = $isHtml ? $this->htmlToText($body) : $body;

        // 「メッセージ内容」セクションを探す
        if (preg_match('/(?:メッセージ(?:内容)?|本文)[：:]\s*\n?(.+?)(?:\n{2,}|---|\*{3}|$)/us', $text, $matches)) {
            $summary = trim($matches[1]);
            // 200文字で切り詰め
            if (mb_strlen($summary) > 200) {
                $summary = mb_substr($summary, 0, 200) . '...';
            }
            return $summary;
        }

        return '';
    }

    /**
     * 選考ステータスを抽出
     */
    private function extractStatus(string $body): string
    {
        $statuses = [
            '書類選考通過' => '書類選考通過',
            '1次面接' => '1次面接',
            '2次面接' => '2次面接',
            '最終面接' => '最終面接',
            '内定' => '内定',
            '不合格' => '不合格',
            '辞退' => '辞退',
        ];

        $text = strip_tags($body);
        foreach ($statuses as $keyword => $status) {
            if (mb_strpos($text, $keyword) !== false) {
                return $status;
            }
        }

        return '';
    }

    /**
     * ビズリーチのURLを抽出
     */
    private function extractBizReachUrl(string $body): ?string
    {
        if (preg_match('/https?:\/\/(?:www\.)?bizreach\.(?:biz|jp)[^\s"\'<>]+/i', $body, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * このメールがビズリーチからの通知かどうかを判定
     */
    public static function isBizReachEmail(string $from): bool
    {
        $bizReachDomains = [
            '@mail.bizreach.biz',
            '@bizreach.biz',
            '@notification.bizreach.biz',
            '@bizreach.jp',
        ];

        foreach ($bizReachDomains as $domain) {
            if (str_contains(strtolower($from), $domain)) {
                return true;
            }
        }

        return false;
    }
}
