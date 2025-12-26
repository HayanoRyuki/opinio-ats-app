<?php

namespace App\Domain\Decide;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * 採用判断エンティティ
 *
 * 採用可否という「意思決定の事実」を表す。
 * Application.status（選考プロセス上の状態）とは独立して扱う。
 */
final class HiringDecision
{
    private int $applicationId;
    private int $decidedBy;
    private string $decision;              // hire | reject | hold
    private ?string $reason;
    private DateTimeImmutable $decidedAt;

    public function __construct(
        int $applicationId,
        int $decidedBy,
        string $decision,
        ?string $reason,
        DateTimeImmutable $decidedAt,
    ) {
        $this->assertDecisionIsValid($decision);

        $this->applicationId = $applicationId;
        $this->decidedBy     = $decidedBy;
        $this->decision      = $decision;
        $this->reason        = $reason;
        $this->decidedAt     = $decidedAt;
    }

    /**
     * --- Getter ---
     */

    public function applicationId(): int
    {
        return $this->applicationId;
    }

    public function decidedBy(): int
    {
        return $this->decidedBy;
    }

    public function decision(): string
    {
        return $this->decision;
    }

    public function reason(): ?string
    {
        return $this->reason;
    }

    public function decidedAt(): DateTimeImmutable
    {
        return $this->decidedAt;
    }

    /**
     * --- Domain Logic ---
     */

    public function isHire(): bool
    {
        return $this->decision === 'hire';
    }

    public function isReject(): bool
    {
        return $this->decision === 'reject';
    }

    public function isHold(): bool
    {
        return $this->decision === 'hold';
    }

    private function assertDecisionIsValid(string $decision): void
    {
        $allowed = ['hire', 'reject', 'hold'];

        if (!in_array($decision, $allowed, true)) {
            throw new InvalidArgumentException(
                "Invalid hiring decision: {$decision}"
            );
        }
    }
}
