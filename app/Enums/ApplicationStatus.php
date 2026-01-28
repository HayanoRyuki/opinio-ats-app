<?php

namespace App\Enums;

/**
 * 応募ステータス
 */
enum ApplicationStatus: string
{
    case ACTIVE = 'active';         // 選考中
    case OFFERED = 'offered';       // 内定
    case HIRED = 'hired';           // 入社
    case REJECTED = 'rejected';     // 不採用
    case WITHDRAWN = 'withdrawn';   // 辞退

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => '選考中',
            self::OFFERED => '内定',
            self::HIRED => '入社',
            self::REJECTED => '不採用',
            self::WITHDRAWN => '辞退',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::HIRED, self::REJECTED, self::WITHDRAWN]);
    }
}
