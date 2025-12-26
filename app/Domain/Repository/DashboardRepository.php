<?php

namespace App\Domain\Repository;

/**
 * DashboardRepository
 *
 * ダッシュボード用に必要な集計素材を提供するリポジトリ。
 */
interface DashboardRepository
{
    /**
     * 選考リードタイム一覧（日数）
     *
     * @return int[]
     */
    public function leadTimes(): array;

    /**
     * 面接官別の通過・評価数
     *
     * 例:
     * [
     *   'interviewerA' => ['passed' => 5, 'total' => 10],
     * ]
     */
    public function interviewerStats(): array;
}
