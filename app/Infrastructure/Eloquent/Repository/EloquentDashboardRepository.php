<?php

namespace App\Infrastructure\Eloquent\Repository;

use App\Domain\Learn\DashboardRepository;
use Illuminate\Support\Facades\DB;

final class EloquentDashboardRepository implements DashboardRepository
{
    public function getHiringFunnel(): array
    {
        /**
         * status（進行）× decision（判断）
         */
        return DB::table('applications')
            ->leftJoin(
                'hiring_decisions',
                'applications.id',
                '=',
                'hiring_decisions.application_id'
            )
            ->select(
                'applications.status',
                'hiring_decisions.decision',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(
                'applications.status',
                'hiring_decisions.decision'
            )
            ->get()
            ->groupBy('status')
            ->map(function ($rows) {
                return $rows->pluck('count', 'decision')->toArray();
            })
            ->toArray();
    }

    public function getKpiSnapshot(): array
    {
        return [
            'applications_total' => DB::table('applications')->count(),

            'decision_hire_rate' => $this->rate(
                DB::table('hiring_decisions')->where('decision', 'hire')->count(),
                DB::table('hiring_decisions')->count()
            ),

            'average_days_to_decision' => DB::table('hiring_decisions')
                ->selectRaw('AVG(DATEDIFF(decided_at, created_at)) as avg_days')
                ->value('avg_days'),
        ];
    }

    private function rate(int $numerator, int $denominator): float
    {
        if ($denominator === 0) {
            return 0.0;
        }

        return round($numerator / $denominator, 3);
    }
}
