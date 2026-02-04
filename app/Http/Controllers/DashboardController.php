<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\IntakeCandidateDraft;
use App\Models\Job;
use App\Models\Interview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->attributes->get('company_id');
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();

        // === 今すぐのアクション ===
        $actions = [
            'schedulePending' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
                ->where('status', 'active')
                ->count(),
            'evaluationPending' => 0, // Interview評価未入力（Interviewモデルがあれば）
            'draftPending' => IntakeCandidateDraft::whereHas('applicationIntake', fn($q) => $q->where('company_id', $companyId))
                ->where('status', 'draft')
                ->count(),
            'longStagnant' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
                ->where('status', 'active')
                ->where('updated_at', '<', $now->copy()->subDays(14))
                ->count(),
        ];

        // === 今週の面接 ===
        $weeklyInterviews = []; // Interviewモデルから取得（あれば）

        // === KPIサマリー ===
        $thisWeekApplications = Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $lastWeekApplications = Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->count();

        $thisWeekHired = Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
            ->where('status', 'hired')
            ->whereBetween('updated_at', [$weekStart, $weekEnd])
            ->count();
        $lastWeekHired = Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
            ->where('status', 'hired')
            ->whereBetween('updated_at', [$lastWeekStart, $lastWeekEnd])
            ->count();

        $kpis = [
            'newApplications' => [
                'value' => $thisWeekApplications,
                'change' => $thisWeekApplications - $lastWeekApplications,
            ],
            'activeApplications' => [
                'value' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
                    ->whereIn('status', ['active', 'offered'])
                    ->count(),
                'change' => 0,
            ],
            'hired' => [
                'value' => $thisWeekHired,
                'change' => $thisWeekHired - $lastWeekHired,
            ],
            'offered' => [
                'value' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
                    ->where('status', 'offered')
                    ->count(),
                'change' => 0,
            ],
        ];

        // === 選考ファネル ===
        $companyFilter = fn($q) => $q->where('company_id', $companyId);
        $funnel = [
            ['stage' => '応募', 'count' => Application::whereHas('candidate', $companyFilter)->count()],
            ['stage' => '書類通過', 'count' => Application::whereHas('candidate', $companyFilter)->whereNotIn('status', ['rejected', 'withdrawn'])->count()],
            ['stage' => '1次面接', 'count' => Application::whereHas('candidate', $companyFilter)->whereIn('status', ['active', 'offered', 'hired'])->count()],
            ['stage' => '2次面接', 'count' => Application::whereHas('candidate', $companyFilter)->whereIn('status', ['active', 'offered', 'hired'])->count()],
            ['stage' => '内定', 'count' => Application::whereHas('candidate', $companyFilter)->whereIn('status', ['offered', 'hired'])->count()],
            ['stage' => '承諾', 'count' => Application::whereHas('candidate', $companyFilter)->where('status', 'hired')->count()],
        ];

        // === チャネル別分析 ===
        $channelStats = Candidate::where('company_id', $companyId)
            ->select('source_channel', DB::raw('count(*) as count'))
            ->groupBy('source_channel')
            ->pluck('count', 'source_channel')
            ->toArray();

        // === 最近のドラフト ===
        $recentDrafts = IntakeCandidateDraft::whereHas('applicationIntake', fn($q) => $q->where('company_id', $companyId))
            ->with(['applicationIntake'])
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'actions' => $actions,
            'weeklyInterviews' => $weeklyInterviews,
            'kpis' => $kpis,
            'funnel' => $funnel,
            'channelStats' => $channelStats,
            'recentDrafts' => $recentDrafts,
        ]);
    }
}
