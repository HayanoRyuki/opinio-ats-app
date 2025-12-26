<?php

namespace App\Domain\Repository;

use App\Domain\Evaluate\Candidate;

/**
 * CandidateRepository
 *
 * 候補者を取得・保存するためのリポジトリ。
 */
interface CandidateRepository
{
    /**
     * 全候補者を取得
     *
     * @return Candidate[]
     */
    public function all(): array;

    /**
     * 候補者を取得
     */
    public function findById(string $candidateId): ?Candidate;

    /**
     * 候補者を保存
     */
    public function save(Candidate $candidate): void;
}
