<?php

namespace App\Enums;

/**
 * 応募取り込みチャネル
 *
 * 4つの応募経路を定義：
 * - direct: 自社採用サイト・LPからの正式応募
 * - scout: スカウトサービス（ビズリーチ・Wantedly等）からの反応（仮応募）
 * - agent: エージェント（人材紹介会社）からの推薦（正式応募）
 * - referral: 社員紹介（正式応募）
 */
enum IntakeChannel: string
{
    case DIRECT = 'direct';         // 直接応募（採用サイト・LP）
    case SCOUT = 'scout';           // スカウト（ビズリーチ・Wantedly・Green等）
    case AGENT = 'agent';           // エージェント推薦
    case REFERRAL = 'referral';     // リファラル（社員紹介）

    public function label(): string
    {
        return match ($this) {
            self::DIRECT => '直接応募',
            self::SCOUT => 'スカウト',
            self::AGENT => 'エージェント',
            self::REFERRAL => 'リファラル',
        };
    }

    /**
     * このチャネルからの応募が「仮応募」として扱われるか
     * スカウト反応は興味ありの段階であり、正式応募ではない
     */
    public function isPreliminary(): bool
    {
        return $this === self::SCOUT;
    }

    /**
     * このチャネルからの応募が「正式応募」として扱われるか
     */
    public function isFormalApplication(): bool
    {
        return !$this->isPreliminary();
    }

    /**
     * このチャネルで履歴書が必須か
     * エージェント経由は書類選考前提のため必須
     */
    public function requiresResume(): bool
    {
        return $this === self::AGENT;
    }
}
