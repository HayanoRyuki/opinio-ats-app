<?php

namespace App\Domain\Decide;

interface HiringDecisionRepository
{
    /**
     * 採用判断を保存する
     */
    public function save(HiringDecision $decision): void;

    /**
     * 応募IDから採用判断を取得する
     *
     * ※ Application.status とは独立した
     *    「意思決定の結果」を取得するための read モデル
     */
    public function findByApplicationId(int $applicationId): ?HiringDecision;
}
