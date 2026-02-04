<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * レポート一覧
     */
    public function index(Request $request)
    {
        $companyId = $request->attributes->get('company_id');

        // 期間パラメータ
        $period = $request->input('period', 'this_month');
        [$startDate, $endDate] = $this->getPeriodDates($period);

        // 比較期間（前期間）
        $periodLength = $startDate->diffInDays($endDate);
        $compareStart = $startDate->copy()->subDays($periodLength + 1);
        $compareEnd = $startDate->copy()->subDay();

        // === パイプラインサマリー ===
        $pipeline = $this->getPipelineSummary($companyId, $startDate, $endDate, $compareStart, $compareEnd);

        // === チャネル効果分析 ===
        $channelAnalysis = $this->getChannelAnalysis($companyId, $startDate, $endDate);

        // === 月別推移（過去6ヶ月） ===
        $monthlyTrend = $this->getMonthlyTrend($companyId);

        // === 求人別サマリー ===
        $jobSummary = $this->getJobSummary($companyId, $startDate, $endDate);

        return Inertia::render('Reports/Index', [
            'period' => $period,
            'periodLabel' => $this->getPeriodLabel($period),
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'pipeline' => $pipeline,
            'channelAnalysis' => $channelAnalysis,
            'monthlyTrend' => $monthlyTrend,
            'jobSummary' => $jobSummary,
        ]);
    }

    /**
     * 期間から日付範囲を取得
     */
    private function getPeriodDates(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_quarter' => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()],
            'last_quarter' => [$now->copy()->subQuarter()->startOfQuarter(), $now->copy()->subQuarter()->endOfQuarter()],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'all' => [Carbon::create(2020, 1, 1), $now->copy()->endOfDay()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    /**
     * 期間ラベル
     */
    private function getPeriodLabel(string $period): string
    {
        return match ($period) {
            'this_month' => '今月',
            'last_month' => '先月',
            'this_quarter' => '今四半期',
            'last_quarter' => '前四半期',
            'this_year' => '今年',
            'all' => '全期間',
            default => '今月',
        };
    }

    /**
     * パイプラインサマリー
     */
    private function getPipelineSummary($companyId, $startDate, $endDate, $compareStart, $compareEnd): array
    {
        $companyFilter = fn($q) => $q->where('company_id', $companyId);

        // 今期間
        $applications = Application::whereHas('candidate', $companyFilter)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $offered = Application::whereHas('candidate', $companyFilter)
            ->where('status', 'offered')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $hired = Application::whereHas('candidate', $companyFilter)
            ->where('status', 'hired')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $rejected = Application::whereHas('candidate', $companyFilter)
            ->where('status', 'rejected')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $withdrawn = Application::whereHas('candidate', $companyFilter)
            ->where('status', 'withdrawn')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        // 前期間（比較用）
        $prevApplications = Application::whereHas('candidate', $companyFilter)
            ->whereBetween('created_at', [$compareStart, $compareEnd])
            ->count();

        $prevHired = Application::whereHas('candidate', $companyFilter)
            ->where('status', 'hired')
            ->whereBetween('updated_at', [$compareStart, $compareEnd])
            ->count();

        // 通過率計算
        $passRate = $applications > 0 ? round(($offered + $hired) / $applications * 100, 1) : 0;

        return [
            'applications' => [
                'value' => $applications,
                'change' => $applications - $prevApplications,
                'changePercent' => $prevApplications > 0 ? round(($applications - $prevApplications) / $prevApplications * 100, 1) : 0,
            ],
            'offered' => [
                'value' => $offered,
            ],
            'hired' => [
                'value' => $hired,
                'change' => $hired - $prevHired,
            ],
            'rejected' => [
                'value' => $rejected,
            ],
            'withdrawn' => [
                'value' => $withdrawn,
            ],
            'passRate' => $passRate,
        ];
    }

    /**
     * チャネル効果分析
     */
    private function getChannelAnalysis($companyId, $startDate, $endDate): array
    {
        $channels = ['direct', 'scout', 'agent', 'referral'];
        $result = [];

        foreach ($channels as $channel) {
            // チャネル別候補者数
            $candidateCount = Candidate::where('company_id', $companyId)
                ->where('source_channel', $channel)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // チャネル別応募数
            $applicationCount = Application::whereHas('candidate', function ($q) use ($companyId, $channel) {
                $q->where('company_id', $companyId)->where('source_channel', $channel);
            })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // チャネル別内定・入社数
            $hiredCount = Application::whereHas('candidate', function ($q) use ($companyId, $channel) {
                $q->where('company_id', $companyId)->where('source_channel', $channel);
            })
                ->whereIn('status', ['offered', 'hired'])
                ->count();

            // 通過率
            $passRate = $applicationCount > 0 ? round($hiredCount / $applicationCount * 100, 1) : 0;

            $result[] = [
                'channel' => $channel,
                'label' => $this->getChannelLabel($channel),
                'candidates' => $candidateCount,
                'applications' => $applicationCount,
                'hired' => $hiredCount,
                'passRate' => $passRate,
            ];
        }

        // 応募数で降順ソート
        usort($result, fn($a, $b) => $b['applications'] - $a['applications']);

        return $result;
    }

    /**
     * 月別推移（過去6ヶ月）
     */
    private function getMonthlyTrend($companyId): array
    {
        $result = [];
        $companyFilter = fn($q) => $q->where('company_id', $companyId);

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $applications = Application::whereHas('candidate', $companyFilter)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $hired = Application::whereHas('candidate', $companyFilter)
                ->where('status', 'hired')
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();

            $result[] = [
                'month' => $monthStart->format('Y年n月'),
                'monthShort' => $monthStart->format('n月'),
                'applications' => $applications,
                'hired' => $hired,
            ];
        }

        return $result;
    }

    /**
     * 求人別サマリー
     */
    private function getJobSummary($companyId, $startDate, $endDate): array
    {
        $jobs = Job::where('company_id', $companyId)
            ->withCount([
                'applications' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                },
                'applications as hired_count' => function ($q) {
                    $q->where('status', 'hired');
                },
                'applications as active_count' => function ($q) {
                    $q->where('status', 'active');
                },
            ])
            ->orderBy('applications_count', 'desc')
            ->limit(10)
            ->get();

        return $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'applications' => $job->applications_count,
                'active' => $job->active_count,
                'hired' => $job->hired_count,
            ];
        })->toArray();
    }

    /**
     * チャネルラベル
     */
    private function getChannelLabel(string $channel): string
    {
        return match ($channel) {
            'direct' => '直接応募',
            'scout' => 'スカウト',
            'agent' => 'エージェント',
            'referral' => 'リファラル',
            default => $channel,
        };
    }
}
