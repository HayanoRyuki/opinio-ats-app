<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PipelineController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->attributes->get('company_id');
        $jobId = $request->input('job_id');

        // 求人一覧（フィルタ用）
        $jobs = Job::where('company_id', $companyId)
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title']);

        // ステータス定義
        $stages = [
            ['key' => 'active', 'label' => '選考中', 'color' => '#4e878c'],
            ['key' => 'offered', 'label' => '内定', 'color' => '#f59e0b'],
            ['key' => 'hired', 'label' => '入社', 'color' => '#65b891'],
            ['key' => 'rejected', 'label' => '不採用', 'color' => '#ef4444'],
            ['key' => 'withdrawn', 'label' => '辞退', 'color' => '#6b7280'],
        ];

        // ステータスごとに応募を取得
        $pipeline = [];
        foreach ($stages as $stage) {
            $query = Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))
                ->where('status', $stage['key'])
                ->with(['candidate', 'job']);

            // 求人フィルタ
            if ($jobId) {
                $query->where('job_id', $jobId);
            }

            $applications = $query->orderBy('updated_at', 'desc')->get();

            $pipeline[] = [
                'key' => $stage['key'],
                'label' => $stage['label'],
                'color' => $stage['color'],
                'count' => $applications->count(),
                'applications' => $applications,
            ];
        }

        // 統計
        $stats = [
            'total' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))->count(),
            'active' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))->where('status', 'active')->count(),
            'hired' => Application::whereHas('candidate', fn($q) => $q->where('company_id', $companyId))->where('status', 'hired')->count(),
        ];

        return Inertia::render('Pipeline/Index', [
            'pipeline' => $pipeline,
            'jobs' => $jobs,
            'selectedJobId' => $jobId,
            'stats' => $stats,
        ]);
    }
}
