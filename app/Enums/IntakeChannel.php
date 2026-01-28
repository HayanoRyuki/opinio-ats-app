<?php

namespace App\Enums;

/**
 * 応募取り込みチャネル
 */
enum IntakeChannel: string
{
    case DIRECT = 'direct';         // 直接応募（採用ページ）
    case MEDIA = 'media';           // メディア経由（求人媒体）
    case AGENT = 'agent';           // エージェント推薦
    case REFERRAL = 'referral';     // リファラル（社員紹介）

    public function label(): string
    {
        return match ($this) {
            self::DIRECT => '直接応募',
            self::MEDIA => 'メディア経由',
            self::AGENT => 'エージェント推薦',
            self::REFERRAL => 'リファラル',
        };
    }
}
