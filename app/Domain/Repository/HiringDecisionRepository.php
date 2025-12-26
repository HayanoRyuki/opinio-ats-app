<?php

namespace App\Domain\Repository;

use App\Domain\Record\HiringDecision;

/**
 * HiringDecisionRepository
 *
 * 採用判断を取得・保存するためのリポジトリ。
 * 永続化の方法（DB / API / CSV 等）は問わない。
 */
interface HiringDecisionRepository
{
    /**
     * 全ての採用判断を取得
     *
     * @return HiringDecision[]
     */
    public function all(): array;

    /**
     * 採用判断を保存
     */
    public function save(HiringDecision $decision): void;
}
