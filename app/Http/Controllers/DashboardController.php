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
            'schedulePending' => Application::where('company_id', $companyId)
                ->where('status', 'in_progress')
                ->whereNull('next_interview_at')
                ->count(),
            'evaluationPending' => 0, // Interview評価未入力（Interviewモデルがあれば）
            'draftPending' => IntakeCandidateDraft::whereHas('applicationIntake', fn($q) => $q->where('company_id', $companyId))
                ->where('status', 'draft')
                ->count(),
            'longStagnant' => Application::where('company_id', $companyId)
                ->where('status', 'in_progress')
                ->where('updated_at', '<', $now->copy()->subDays(14))
                ->count(),
        ];

        // === 今週の面接 ===
        $weeklyInterviews = []; // Interviewモデルから取得（あれば）

        // === KPIサマリー ===
        $thisWeekApplications = Application::where('company_id', $companyId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $lastWeekApplications = Application::where('company_id', $companyId)
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->count();

        $thisWeekHired = Application::where('company_id', $companyId)
            ->where('status', 'hired')
            ->whereBetween('updated_at', [$weekStart, $weekEnd])
            ->count();
        $lastWeekHired = Application::where('company_id', $companyId)
            ->where('status', 'hired')
            ->whereBetween('updated_at', [$lastWeekStart, $lastWeekEnd])
            ->count();

        $kpis = [
            'newApplications' => [
                'value' => $thisWeekApplications,
                'change' => $thisWeekApplications - $lastWeekApplications,
            ],
            'activeApplications' => [
                'value' => Application::where('company_id', $companyId)
                    ->whereIn('status', ['applied', 'in_progress'])
                    ->count(),
                'change' => 0,
            ],
            'hired' => [
                'value' => $thisWeekHired,
                'change' => $thisWeekHired - $lastWeekHired,
            ],
            'offered' => [
                'value' => Application::where('company_id', $companyId)
                    ->where('status', 'offered')
                    ->count(),
                'change' => 0,
            ],
        ];

        // === 選考ファネル ===
        $funnel = [
            ['stage' => '応募', 'count' => Application::where('company_id', $companyId)->count()],
            ['stage' => '書類通過', 'count' => Application::where('company_id', $companyId)->whereNotIn('status', ['rejected'])->where('current_step', '>=', 1)->count()],
            ['stage' => '1次面接', 'count' => Application::where('company_id', $companyId)->whereIn('status', ['in_progress', 'offered', 'hired'])->count()],
            ['stage' => '2次面接', 'count' => Application::where('company_id', $companyId)->whereIn('status', ['in_progress', 'offered', 'hired'])->where('current_step', '>=', 2)->count()],
            ['stage' => '内定', 'count' => Application::where('company_id', $companyId)->whereIn('status', ['offered', 'hired'])->count()],
            ['stage' => '承諾', 'count' => Application::where('company_id', $companyId)->where('status', 'hired')->count()],
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
