<?php

namespace App\Domain\Learn;

use App\Domain\Record\HiringDecision;

/**
 * KPI
 *
 * 採用活動の状態を示す主要指標を表すドメインオブジェクト。
 * 単なる集計結果ではなく、
 * 採用判断がうまく機能しているかを判断するための指標。
 */
final class KPI
{
    private float $offerAcceptanceRate;
    private float $interviewPassRate;
    private float $averageLeadTime;
    private array $interviewerPassRates;

    private function __construct(
        float $offerAcceptanceRate,
        float $interviewPassRate,
        float $averageLeadTime,
        array $interviewerPassRates
    ) {
        $this->offerAcceptanceRate = $offerAcceptanceRate;
        $this->interviewPassRate = $interviewPassRate;
        $this->averageLeadTime = $averageLeadTime;
        $this->interviewerPassRates = $interviewerPassRates;
    }

    /**
     * KPIを生成する
     *
     * @param array $decisions HiringDecision の配列
     * @param array $leadTimes 選考リードタイム（候補者ごとの日数）
     * @param array $interviewerStats 面接官別の通過数・評価数
     */
    public static function calculate(
        array $decisions,
        array $leadTimes,
        array $interviewerStats
    ): self {
        $offerAcceptanceRate = self::calculateOfferAcceptanceRate($decisions);
        $interviewPassRate = self::calculateInterviewPassRate($decisions);
        $averageLeadTime = self::calculateAverageLeadTime($leadTimes);
        $interviewerPassRates = self::calculateInterviewerPassRates($interviewerStats);

        return new self(
            $offerAcceptanceRate,
            $interviewPassRate,
            $averageLeadTime,
            $interviewerPassRates
        );
    }

    /**
     * 内定承諾率を算出
     */
    private static function calculateOfferAcceptanceRate(array $decisions): float
    {
        $offers = array_filter($decisions, fn (HiringDecision $d) => $d->decision() === 'hire');

        if (count($offers) === 0) {
            return 0.0;
        }

        $accepted = array_filter(
            $offers,
            fn (HiringDecision $d) => $d->isConfident()
        );

        return round(count($accepted) / count($offers), 2);
    }

    /**
     * 面接通過率を算出
     */
    private static function calculateInterviewPassRate(array $decisions): float
    {
        if (count($decisions) === 0) {
            return 0.0;
        }

        $passed = array_filter(
            $decisions,
            fn (HiringDecision $d) => $d->decision() !== 'no_hire'
        );

        return round(count($passed) / count($decisions), 2);
    }

    /**
     * 平均リードタイムを算出
     */
    private static function calculateAverageLeadTime(array $leadTimes): float
    {
        if (count($leadTimes) === 0) {
            return 0.0;
        }

        return round(array_sum($leadTimes) / count($leadTimes), 1);
    }

    /**
     * 面接官別通過率を算出
     *
     * @param array $stats
     *   例:
     *   [
     *     'interviewerA' => ['passed' => 5, 'total' => 10],
     *     'interviewerB' => ['passed' => 8, 'total' => 12],
     *   ]
     */
    private static function calculateInterviewerPassRates(array $stats): array
    {
        $rates = [];

        foreach ($stats as $interviewerId => $data) {
            if ($data['total'] === 0) {
                $rates[$interviewerId] = 0.0;
                continue;
            }

            $rates[$interviewerId] = round(
                $data['passed'] / $data['total'],
                2
            );
        }

        return $rates;
    }

    /**
     * KPI取得系
     */
    public function offerAcceptanceRate(): float
    {
        return $this->offerAcceptanceRate;
    }

    public function interviewPassRate(): float
    {
        return $this->interviewPassRate;
    }

    public function averageLeadTime(): float
    {
        return $this->averageLeadTime;
    }

    public function interviewerPassRates(): array
    {
        return $this->interviewerPassRates;
    }
}
