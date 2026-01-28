<?php

namespace App\Enums;

/**
 * Role は Auth が決定する権限の列挙。
 * ATS 側では「解釈のみ」を行い、生成・変更はしない。
 */
enum Role: string
{
    case VENDOR      = 'vendor';       // 外部提供者・将来拡張用
    case ADMIN       = 'admin';        // 会社管理者
    case RECRUITER   = 'recruiter';    // 採用担当
    case INTERVIEWER = 'interviewer';  // 面接官
    case VIEWER      = 'viewer';       // 閲覧のみ（将来 or 既存なら有効）

    /**
     * 社内ユーザーか（vendor を除外）
     */
    public function isCompanyUser(): bool
    {
        return $this !== self::VENDOR;
    }

    /**
     * 採用フローを管理できるか
     * （求人作成・候補者操作など）
     */
    public function canManageRecruitment(): bool
    {
        return in_array($this, [
            self::ADMIN,
            self::RECRUITER,
        ], true);
    }

    /**
     * 面接に関与できるか
     */
    public function canInterview(): bool
    {
        return in_array($this, [
            self::ADMIN,
            self::RECRUITER,
            self::INTERVIEWER,
        ], true);
    }

    /**
     * ATS にログイン可能な role 一覧
     * （Gate / middleware 用）
     */
    public static function atsUsers(): array
    {
        return [
            self::ADMIN->value,
            self::RECRUITER->value,
            self::INTERVIEWER->value,
            self::VIEWER->value,
        ];
    }

    /**
     * 採用管理が可能な role 一覧
     */
    public static function recruitmentManagers(): array
    {
        return [
            self::ADMIN->value,
            self::RECRUITER->value,
        ];
    }
}
