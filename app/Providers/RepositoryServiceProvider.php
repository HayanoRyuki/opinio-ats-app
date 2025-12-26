<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Decide Domain
use App\Domain\Decide\HiringDecisionRepository;
use App\Infrastructure\Eloquent\Repository\EloquentHiringDecisionRepository;

// Record Domain
use App\Domain\Record\ApplicationRepository;
use App\Infrastructure\Eloquent\Repository\EloquentApplicationRepository;

// Learn Domain
use App\Domain\Learn\DashboardRepository;
use App\Infrastructure\Eloquent\Repository\EloquentDashboardRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings.
     *
     * Domain ごとに Repository を分離する方針。
     * 書く（Record / Decide）・読む（Learn）を明確に分ける。
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Decide Domain
        |--------------------------------------------------------------------------
        | 採用可否という「意思決定」
        */
        $this->app->bind(
            HiringDecisionRepository::class,
            EloquentHiringDecisionRepository::class
        );

        /*
        |--------------------------------------------------------------------------
        | Record Domain
        |--------------------------------------------------------------------------
        | 応募プロセス上の「事実」を扱う
        */
        $this->app->bind(
            ApplicationRepository::class,
            EloquentApplicationRepository::class
        );

        /*
        |--------------------------------------------------------------------------
        | Learn Domain
        |--------------------------------------------------------------------------
        | 判断 × 進行を横断して「読む」
        */
        $this->app->bind(
            DashboardRepository::class,
            EloquentDashboardRepository::class
        );
    }
}
