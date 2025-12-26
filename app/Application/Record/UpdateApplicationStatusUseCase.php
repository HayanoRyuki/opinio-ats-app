<?php

namespace App\Application\Record;

use App\Domain\Record\ApplicationRepository;
use App\Domain\Record\ApplicationStatus;
use InvalidArgumentException;

final class UpdateApplicationStatusUseCase
{
    public function __construct(
        private ApplicationRepository $repository
    ) {}

    /**
     * 応募ステータスを更新する（事実の記録）
     */
    public function execute(int $applicationId, string $status): void
    {
        if (!in_array($status, ApplicationStatus::all(), true)) {
            throw new InvalidArgumentException(
                "Invalid application status: {$status}"
            );
        }

        $this->repository->updateStatus($applicationId, $status);
    }
}
