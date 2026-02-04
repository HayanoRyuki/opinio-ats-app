<?php

namespace App\Enums;

/**
 * 取り込みステータス
 *
 * フロー：
 * RECEIVED → PROCESSING → PENDING → DRAFT → CONFIRMED/REJECTED/DUPLICATE
 */
enum IntakeStatus: string
{
    case RECEIVED = 'received';     // 受信済み（API受付直後）
    case PROCESSING = 'processing'; // 処理中（パース・検証中）
    case PENDING = 'pending';       // 未処理（確認待ち）
    case DRAFT = 'draft';           // ドラフト作成済み
    case CONFIRMED = 'confirmed';   // 確定済み（SoT 登録完了）
    case REJECTED = 'rejected';     // 却下
    case DUPLICATE = 'duplicate';   // 重複

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => '受信済み',
            self::PROCESSING => '処理中',
            self::PENDING => '未処理',
            self::DRAFT => 'ドラフト',
            self::CONFIRMED => '確定済み',
            self::REJECTED => '却下',
            self::DUPLICATE => '重複',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::CONFIRMED, self::REJECTED, self::DUPLICATE]);
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::RECEIVED => in_array($next, [self::PROCESSING, self::PENDING]),
            self::PROCESSING => in_array($next, [self::PENDING, self::REJECTED]),
            self::PENDING => in_array($next, [self::DRAFT, self::REJECTED, self::DUPLICATE]),
            self::DRAFT => in_array($next, [self::CONFIRMED, self::REJECTED, self::DUPLICATE]),
            default => false,
        };
    }
}
