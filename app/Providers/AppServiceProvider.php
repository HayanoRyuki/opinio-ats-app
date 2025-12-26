<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Decide\HiringDecisionRepository;
use App\Infrastructure\Eloquent\Repository\EloquentHiringDecisionRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            HiringDecisionRepository::class,
            EloquentHiringDecisionRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
