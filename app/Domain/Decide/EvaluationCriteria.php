<?php

namespace App\Domain\Decide;

/**
 * EvaluationCriteria
 *
 * 採用における評価基準を表すドメインオブジェクト。
 * 「何をもって良いと判断するか」を事前に定義するためのもの。
 */
final class EvaluationCriteria
{
    private string $criteriaId;
    private string $position;
    private array $items;
    private \DateTimeImmutable $definedAt;

    private function __construct(
        string $criteriaId,
        string $position,
        array $items,
        \DateTimeImmutable $definedAt
    ) {
        $this->criteriaId = $criteriaId;
        $this->position = $position;
        $this->items = $items;
        $this->definedAt = $definedAt;
    }

    /**
     * 評価基準を定義する
     *
     * @param string $criteriaId 評価基準ID
     * @param string $position 対象ポジション
     * @param array $items 評価項目
     *   例:
     *   [
     *     'skill' => [
     *       'label' => 'スキル',
     *       'weight' => 40,
     *       'description' => '業務に必要な技術的スキル'
     *     ],
     *     'experience' => [
     *       'label' => '経験',
     *       'weight' => 30,
     *       'description' => '関連業務の実務経験'
     *     ],
     *     'culture_fit' => [
     *       'label' => 'カルチャーフィット',
     *       'weight' => 30,
     *       'description' => '価値観や働き方の一致'
     *     ]
     *   ]
     */
    public static function define(
        string $criteriaId,
        string $position,
        array $items
    ): self {
        self::validateWeights($items);

        return new self(
            $criteriaId,
            $position,
            $items,
            new \DateTimeImmutable()
        );
    }

    /**
     * 評価項目を取得
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * ポジション名を取得
     */
    public function position(): string
    {
        return $this->position;
    }

    /**
     * 評価基準IDを取得
     */
    public function id(): string
    {
        return $this->criteriaId;
    }

    /**
     * 定義日時を取得
     */
    public function definedAt(): \DateTimeImmutable
    {
        return $this->definedAt;
    }

    /**
     * 評価基準の重みが妥当か検証する
     */
    private static function validateWeights(array $items): void
    {
        $totalWeight = array_sum(array_column($items, 'weight'));

        if ($totalWeight !== 100) {
            throw new \InvalidArgumentException(
                'Total weight of evaluation criteria must be 100.'
            );
        }
    }
}
