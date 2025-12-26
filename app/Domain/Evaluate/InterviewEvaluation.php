<?php

namespace App\Domain\Evaluate;

/**
 * InterviewEvaluation
 *
 * 面接における人の評価を表すドメインオブジェクト。
 * 感想ではなく、
 * 「どの観点で、どう判断したか」を構造として残す。
 */
final class InterviewEvaluation
{
    private string $interviewerId;
    private array $criteriaScores;
    private string $overallImpression;
    private ?string $recommendation;
    private \DateTimeImmutable $evaluatedAt;

    private function __construct(
        string $interviewerId,
        array $criteriaScores,
        string $overallImpression,
        ?string $recommendation,
        \DateTimeImmutable $evaluatedAt
    ) {
        $this->interviewerId = $interviewerId;
        $this->criteriaScores = $criteriaScores;
        $this->overallImpression = $overallImpression;
        $this->recommendation = $recommendation;
        $this->evaluatedAt = $evaluatedAt;
    }

    /**
     * 面接評価を作成する
     *
     * @param string $interviewerId 面接官ID
     * @param array $criteriaScores 評価項目ごとのスコア
     *   例:
     *   [
     *     'skill' => ['score' => 4, 'comment' => '実務経験が豊富'],
     *     'communication' => ['score' => 3, 'comment' => '受け答えは安定している'],
     *     'culture_fit' => ['score' => 2, 'comment' => '価値観に一部ズレあり']
     *   ]
     * @param string $overallImpression 総合所感
     * @param string|null $recommendation 採用判断の方向性（hire / no_hire / hold）
     */
    public static function create(
        string $interviewerId,
        array $criteriaScores,
        string $overallImpression,
        ?string $recommendation = null
    ): self {
        return new self(
            $interviewerId,
            $criteriaScores,
            $overallImpression,
            $recommendation,
            new \DateTimeImmutable()
        );
    }

    /**
     * 面接官IDを取得
     */
    public function interviewerId(): string
    {
        return $this->interviewerId;
    }

    /**
     * 評価項目ごとのスコアを取得
     */
    public function criteriaScores(): array
    {
        return $this->criteriaScores;
    }

    /**
     * 総合所感を取得
     */
    public function overallImpression(): string
    {
        return $this->overallImpression;
    }

    /**
     * 推奨判断を取得
     */
    public function recommendation(): ?string
    {
        return $this->recommendation;
    }

    /**
     * 評価日時を取得
     */
    public function evaluatedAt(): \DateTimeImmutable
    {
        return $this->evaluatedAt;
    }

    /**
     * この評価が採用判断に使える状態か
     */
    public function isComplete(): bool
    {
        return !empty($this->criteriaScores) && !empty($this->overallImpression);
    }
}
