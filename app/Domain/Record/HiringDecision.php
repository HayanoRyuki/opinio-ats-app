<?php

namespace App\Domain\Record;

use App\Domain\Evaluate\Candidate;
use App\Domain\Evaluate\MatchScore;
use App\Domain\Evaluate\InterviewEvaluation;

/**
 * HiringDecision
 *
 * 採用における最終意思決定を表すドメインオブジェクト。
 * 「採用／不採用」という結果ではなく、
 * なぜその判断に至ったかを構造として残す。
 */
final class HiringDecision
{
    private string $decision; // hire / no_hire / hold
    private string $decidedBy;
    private array $reasons;
    private \DateTimeImmutable $decidedAt;

    private function __construct(
        string $decision,
        string $decidedBy,
        array $reasons,
        \DateTimeImmutable $decidedAt
    ) {
        if (!in_array($decision, ['hire', 'no_hire', 'hold'], true)) {
            throw new \InvalidArgumentException('Invalid hiring decision.');
        }

        $this->decision = $decision;
        $this->decidedBy = $decidedBy;
        $this->reasons = $reasons;
        $this->decidedAt = $decidedAt;
    }

    /**
     * 採用判断を確定する
     *
     * @param string $decision hire / no_hire / hold
     * @param string $decidedBy 判断者ID（採用責任者など）
     * @param array $reasons 判断理由
     *   例:
     *   [
     *     'match_score' => '基準スコアを上回っている',
     *     'interview' => '複数面接官が高評価',
     *     'risk' => '懸念点は許容範囲'
     *   ]
     */
    public static function decide(
        string $decision,
        string $decidedBy,
        array $reasons
    ): self {
        return new self(
            $decision,
            $decidedBy,
            $reasons,
            new \DateTimeImmutable()
        );
    }

    /**
     * 判断結果を取得
     */
    public function decision(): string
    {
        return $this->decision;
    }

    /**
     * 判断理由を取得
     */
    public function reasons(): array
    {
        return $this->reasons;
    }

    /**
     * 判断者を取得
     */
    public function decidedBy(): string
    {
        return $this->decidedBy;
    }

    /**
     * 判断日時を取得
     */
    public function decidedAt(): \DateTimeImmutable
    {
        return $this->decidedAt;
    }

    /**
     * 採用判断が「確信を持てるもの」か
     */
    public function isConfident(): bool
    {
        return $this->decision === 'hire' && count($this->reasons) >= 2;
    }
}
