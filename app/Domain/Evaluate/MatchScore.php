<?php

namespace App\Domain\Evaluate;

/**
 * MatchScore
 *
 * 候補者が評価基準にどの程度マッチしているかを表す値オブジェクト。
 * 単なる点数ではなく、
 * 「なぜこのスコアになったか」を説明できる構造を持つ。
 */
final class MatchScore
{
    private int $score;              // 0 - 100
    private array $breakdown;        // 評価項目ごとの内訳
    private string $evaluatedBy;     // human / ai / rule
    private \DateTimeImmutable $evaluatedAt;

    private function __construct(
        int $score,
        array $breakdown,
        string $evaluatedBy,
        \DateTimeImmutable $evaluatedAt
    ) {
        if ($score < 0 || $score > 100) {
            throw new \InvalidArgumentException('Match score must be between 0 and 100.');
        }

        $this->score = $score;
        $this->breakdown = $breakdown;
        $this->evaluatedBy = $evaluatedBy;
        $this->evaluatedAt = $evaluatedAt;
    }

    /**
     * マッチスコアを生成する
     *
     * @param int $score 総合スコア（0-100）
     * @param array $breakdown 評価項目ごとの内訳
     *   例:
     *   [
     *     'skill' => ['score' => 80, 'reason' => '必須スキルを満たしている'],
     *     'experience' => ['score' => 70, 'reason' => '類似職種の経験あり'],
     *     'culture_fit' => ['score' => 60, 'reason' => 'カルチャー要件は一部一致']
     *   ]
     * @param string $evaluatedBy 評価主体（human / ai / rule）
     */
    public static function create(
        int $score,
        array $breakdown,
        string $evaluatedBy
    ): self {
        return new self(
            $score,
            $breakdown,
            $evaluatedBy,
            new \DateTimeImmutable()
        );
    }

    /**
     * 総合スコアを取得
     */
    public function score(): int
    {
        return $this->score;
    }

    /**
     * 評価内訳を取得
     */
    public function breakdown(): array
    {
        return $this->breakdown;
    }

    /**
     * 評価主体を取得
     */
    public function evaluatedBy(): string
    {
        return $this->evaluatedBy;
    }

    /**
     * 評価日時を取得
     */
    public function evaluatedAt(): \DateTimeImmutable
    {
        return $this->evaluatedAt;
    }

    /**
     * このスコアが採用判断の参考に値するか
     */
    public function isReliable(): bool
    {
        return $this->score >= 50;
    }
}
