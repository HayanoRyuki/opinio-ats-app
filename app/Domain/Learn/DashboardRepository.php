<?php

namespace App\Domain\Learn;

interface DashboardRepository
{
    /**
     * 採用ファネル（進行 × 判断）を取得
     */
    public function getHiringFunnel(): array;

    /**
     * KPI スナップショットを取得
     */
    public function getKpiSnapshot(): array;
}
