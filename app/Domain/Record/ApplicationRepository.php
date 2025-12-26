<?php

namespace App\Domain\Record;

interface ApplicationRepository
{
    /**
     * 応募のステータスを更新する（事実のみ）
     */
    public function updateStatus(int $applicationId, string $status): void;
}
