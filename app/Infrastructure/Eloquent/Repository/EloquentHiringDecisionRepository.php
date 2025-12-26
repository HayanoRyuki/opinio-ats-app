<?php

namespace App\Infrastructure\Eloquent\Repository;

use App\Domain\Decide\HiringDecision as DomainHiringDecision;
use App\Domain\Decide\HiringDecisionRepository;
use App\Models\HiringDecision as EloquentHiringDecision;
use DateTimeImmutable;

final class EloquentHiringDecisionRepository implements HiringDecisionRepository
{
    public function save(DomainHiringDecision $decision): void
    {
        EloquentHiringDecision::updateOrCreate(
            ['application_id' => $decision->applicationId()],
            [
                'decided_by' => $decision->decidedBy(),
                'decision'   => $decision->decision(),
                'reason'     => $decision->reason(),
                'decided_at' => $decision->decidedAt(),
            ]
        );
    }

    public function findByApplicationId(int $applicationId): ?DomainHiringDecision
    {
        $record = EloquentHiringDecision::where('application_id', $applicationId)->first();

        if (! $record) {
            return null;
        }

        return new DomainHiringDecision(
            applicationId: $record->application_id,
            decidedBy: $record->decided_by,
            decision: $record->decision,
            reason: $record->reason,
            decidedAt: DateTimeImmutable::createFromMutable(
                $record->decided_at
            ),
        );
    }
}
