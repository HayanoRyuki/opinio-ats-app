<?php

namespace App\Application\Dashboard;

use App\Domain\Learn\KPI;
use App\Domain\Record\HiringDecision;

/**
 * GetDashboardKPIUseCase
 *
 * ダッシュボード表示用の KPI を生成するユースケース。
 * Domain を組み合わせて「今の採用はうまくいっているか」に答える。
 */
final class GetDashboardKPIUseCase
{
    /**
     * KPI を取得する
     *
     * @param HiringDecision[] $decisions
     * @param array $leadTimes
     * @param array $interviewerStats
     */
    public function execute(
        array $decisions,
        array $leadTimes,
        array $interviewerStats
    ): KPI {
        return KPI::calculate(
            $decisions,
            $leadTimes,
            $interviewerStats
        );
    }
}
