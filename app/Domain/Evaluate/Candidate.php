<?php

namespace App\Domain\Evaluate;

/**
 * Candidate
 *
 * 採用プロセスにおける「候補者」を表すドメインオブジェクト。
 * 候補者は単なる個人情報ではなく、
 * 評価・判断・記録の起点となる存在。
 */
final class Candidate
{
    private string $candidateId;
    private string $name;
    private array $profile;
    private array $documents;
    private array $evaluations = [];
    private ?MatchScore $latestMatchScore = null;

    private function __construct(
        string $candidateId,
        string $name,
        array $profile,
        array $documents
    ) {
        $this->candidateId = $candidateId;
        $this->name = $name;
        $this->profile = $profile;
        $this->documents = $documents;
    }

    /**
     * 候補者を生成する
     */
    public static function create(
        string $candidateId,
        string $name,
        array $profile = [],
        array $documents = []
    ): self {
        return new self(
            $candidateId,
            $name,
            $profile,
            $documents
        );
    }

    /**
     * 書類・プロフィール情報をもとにマッチ度を算出する
     */
    public function evaluateMatch(MatchScore $matchScore): void
    {
        $this->latestMatchScore = $matchScore;
    }

    /**
     * 面接評価を追加する
     */
    public function addEvaluation(InterviewEvaluation $evaluation): void
    {
        $this->evaluations[] = $evaluation;
    }

    /**
     * 最新のマッチスコアを取得
     */
    public function latestMatchScore(): ?MatchScore
    {
        return $this->latestMatchScore;
    }

    /**
     * 面接評価一覧を取得
     */
    public function evaluations(): array
    {
        return $this->evaluations;
    }

    /**
     * 候補者IDを取得
     */
    public function id(): string
    {
        return $this->candidateId;
    }

    /**
     * 候補者名を取得
     */
    public function name(): string
    {
        return $this->name;
    }
}
