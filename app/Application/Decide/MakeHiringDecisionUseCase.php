<?php

namespace App\Application\Decide;

use App\Domain\Decide\HiringDecision;
use App\Domain\Decide\HiringDecisionRepository;
use DateTimeImmutable;

final class MakeHiringDecisionUseCase
{
    public function __construct(
        private HiringDecisionRepository $repository,
    ) {}

    /**
     * 採用判断を確定する
     *
     * Application.status には触れない。
     * 採用可否という「意思決定」を独立した Domain として記録する。
     *
     * @throws \InvalidArgumentException
     */
    public function execute(
        int $applicationId,
        string $decision,            // hire | reject | hold
        int $decidedBy,
        ?string $reason = null,
        ?DateTimeImmutable $decidedAt = null,
    ): void {
        $this->assertDecisionIsValid($decision);

        $decisionEntity = new HiringDecision(
            applicationId: $applicationId,
            decidedBy: $decidedBy,
            decision: $decision,
            reason: $reason,
            decidedAt: $decidedAt ?? new DateTimeImmutable(),
        );

        $this->repository->save($decisionEntity);
    }

    private function assertDecisionIsValid(string $decision): void
    {
        $allowed = ['hire', 'reject', 'hold'];

        if (!in_array($decision, $allowed, true)) {
            throw new \InvalidArgumentException(
                "Invalid decision: {$decision}"
            );
        }
    }
}
