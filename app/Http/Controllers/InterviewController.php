<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->attributes->get('company_id');
        $filter = $request->input('filter', 'all');

        // 選考中の応募を取得（面接予定として表示）
        $query = Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
            ->where('status', 'active')
            ->with(['candidate', 'job']);

        // フィルタ適用
        $now = Carbon::now();
        if ($filter === 'today') {
            $query->whereDate('updated_at', $now->toDateString());
        } elseif ($filter === 'this_week') {
            $query->whereBetween('updated_at', [$now->startOfWeek(), $now->endOfWeek()]);
        } elseif ($filter === 'overdue') {
            $query->where('updated_at', '<', $now->subDays(7));
        }

        $interviews = $query->orderBy('updated_at', 'desc')->get();

        // 統計
        $stats = [
            'total' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
                ->where('status', 'active')
                ->count(),
            'thisWeek' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
                ->where('status', 'active')
                ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'overdue' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
                ->where('status', 'active')
                ->where('updated_at', '<', Carbon::now()->subDays(7))
                ->count(),
        ];

        return Inertia::render('Interviews/Index', [
            'interviews' => $interviews,
            'filter' => $filter,
            'stats' => $stats,
        ]);
    }

    public function show(Request $request, $id)
    {
        $companyId = $request->attributes->get('company_id');

        $application = Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
            ->with(['candidate', 'job'])
            ->findOrFail($id);

        return Inertia::render('Interviews/Show', [
            'application' => $application,
        ]);
    }
}
