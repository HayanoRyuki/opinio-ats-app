<?php

namespace App\Enums;

enum Role: string
{
    case Vendor      = 'vendor';
    case Admin       = 'admin';
    case Recruiter   = 'recruiter';
    case Interviewer = 'interviewer';

    /**
     * 社内ユーザーか（vendor を除外）
     */
    public function isCompanyUser(): bool
    {
        return $this !== self::Vendor;
    }

    /**
     * 採用フローを管理する主体か
     */
    public function canManageRecruitment(): bool
    {
        return in_array($this, [
            self::Admin,
            self::Recruiter,
        ], true);
    }
}
