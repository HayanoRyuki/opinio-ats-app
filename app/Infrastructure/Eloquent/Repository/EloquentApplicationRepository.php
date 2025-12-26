<?php

namespace App\Infrastructure\Eloquent\Repository;

use App\Domain\Record\ApplicationRepository;
use App\Models\Application;

final class EloquentApplicationRepository implements ApplicationRepository
{
    public function updateStatus(int $applicationId, string $status): void
    {
        Application::where('id', $applicationId)
            ->update(['status' => $status]);
    }
}
