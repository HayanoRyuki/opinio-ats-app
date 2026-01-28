<?php

namespace App\Enums;

/**
 * 取り込みステータス
 */
enum IntakeStatus: string
{
    case PENDING = 'pending';       // 未処理
    case DRAFT = 'draft';           // ドラフト作成済み
    case CONFIRMED = 'confirmed';   // 確定済み（SoT 登録完了）
    case REJECTED = 'rejected';     // 却下
    case DUPLICATE = 'duplicate';   // 重複

    public function label(): string
    {
        return match ($this) {
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
}
