<?php

namespace App\Enums;

/**
 * 求人ステータス
 */
enum JobStatus: string
{
    case DRAFT = 'draft';           // 下書き
    case OPEN = 'open';             // 募集中
    case PAUSED = 'paused';         // 一時停止
    case CLOSED = 'closed';         // 募集終了

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => '下書き',
            self::OPEN => '募集中',
            self::PAUSED => '一時停止',
            self::CLOSED => '募集終了',
        };
    }

    public function isAcceptingApplications(): bool
    {
        return $this === self::OPEN;
    }
}
