<?php

namespace App\Http\Controllers\Web;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationStepStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    /**
     * 応募一覧
     */
    public function index(Request $request): Response
    {
        $companyId = $request->attributes->get('company_id');
        $userId = $request->attributes->get('auth_user_id');

        $query = Application::whereHas('candidate', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
            ->with(['candidate.person', 'job', 'steps.selectionStep']);

        // ステータスフィルタ
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // 求人フィルタ
        if ($jobId = $request->input('job_id')) {
            $query->where('job_id', $jobId);
        }

        // 検索（候補者名）
        if ($search = $request->input('search')) {
            $query->whereHas('candidate.person', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderBy('applied_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // 統計
        $baseQuery = Application::whereHas('candidate', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'offered' => (clone $baseQuery)->where('status', 'offered')->count(),
            'hired' => (clone $baseQuery)->where('status', 'hired')->count(),
        ];

        // 求人リスト（フィルタ用）
        $jobs = \App\Models\Job::where('company_id', $companyId)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return Inertia::render('Applications/Index', [
            'applications' => $applications,
            'stats' => $stats,
            'jobs' => $jobs,
            'filters' => [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'job_id' => $request->input('job_id'),
            ],
        ]);
    }

    /**
     * 応募詳細（選考進捗画面）
     */
    public function show(Request $request, Application $application): Response
    {
        $companyId = $request->attributes->get('company_id');
        $userId = $request->attributes->get('auth_user_id');

        if ($application->candidate->company_id !== $companyId) {
            abort(403);
        }

        $application->load([
            'candidate.person',
            'job.selectionSteps' => function ($q) {
                $q->active()->ordered();
            },
            'steps.selectionStep',
            'steps.evaluator',
        ]);

        // 選考ステップの進捗を整形
        $selectionSteps = $application->job->selectionSteps;
        $applicationSteps = $application->steps->keyBy('selection_step_id');

        $stepsProgress = $selectionSteps->map(function ($step) use ($applicationSteps, $application) {
            $appStep = $applicationSteps->get($step->id);

            return [
                'selection_step' => $step,
                'application_step' => $appStep,
                'status' => $appStep?->status?->value ?? 'not_started',
                'status_label' => $appStep?->status?->label() ?? '未開始',
            ];
        });

        return Inertia::render('Applications/Show', [
            'application' => $application,
            'stepsProgress' => $stepsProgress,
        ]);
    }

    /**
     * 選考ステップを開始
     */
    public function startStep(Request $request, Application $application, int $stepId)
    {
        $companyId = $request->attributes->get('company_id');
        $userId = $request->attributes->get('auth_user_id');

        if ($application->candidate->company_id !== $companyId) {
            abort(403);
        }

        $selectionStep = $application->job->selectionSteps()
            ->where('id', $stepId)
            ->firstOrFail();

        // ApplicationStep を作成または更新
        $appStep = ApplicationStep::updateOrCreate(
            [
                'application_id' => $application->id,
                'selection_step_id' => $selectionStep->id,
            ],
            [
                'status' => ApplicationStepStatus::IN_PROGRESS,
                'started_at' => now(),
            ]
        );

        // 応募の current_step を更新
        $application->update(['current_step' => $selectionStep->name]);

        return back()->with('success', "{$selectionStep->name}を開始しました。");
    }

    /**
     * 選考ステップを完了（通過/不通過）
     */
    public function completeStep(Request $request, Application $application, int $stepId)
    {
        $companyId = $request->attributes->get('company_id');
        $userId = $request->attributes->get('auth_user_id');

        if ($application->candidate->company_id !== $companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'result' => ['required', 'in:passed,failed'],
            'notes' => ['nullable', 'string'],
            'evaluation' => ['nullable', 'array'],
        ]);

        $selectionStep = $application->job->selectionSteps()
            ->where('id', $stepId)
            ->firstOrFail();

        $appStep = ApplicationStep::where('application_id', $application->id)
            ->where('selection_step_id', $selectionStep->id)
            ->firstOrFail();

        DB::transaction(function () use ($appStep, $validated, $userId, $application, $selectionStep) {
            $status = $validated['result'] === 'passed'
                ? ApplicationStepStatus::PASSED
                : ApplicationStepStatus::FAILED;

            $appStep->update([
                'status' => $status,
                'completed_at' => now(),
                'evaluated_by' => $userId,
                'notes' => $validated['notes'] ?? null,
                'evaluation' => $validated['evaluation'] ?? null,
            ]);

            // 不通過の場合、応募自体を不採用に
            if ($validated['result'] === 'failed') {
                $application->update([
                    'status' => ApplicationStatus::REJECTED,
                ]);
            } else {
                // 通過の場合、次のステップがあるか確認
                $nextStep = $application->job->selectionSteps()
                    ->where('order', '>', $selectionStep->order)
                    ->active()
                    ->ordered()
                    ->first();

                if ($nextStep) {
                    $application->update(['current_step' => $nextStep->name]);
                } else {
                    // 最終ステップ通過 = 内定
                    $application->update([
                        'status' => ApplicationStatus::OFFERED,
                    ]);
                }
            }
        });

        $resultLabel = $validated['result'] === 'passed' ? '通過' : '不通過';
        return back()->with('success', "{$selectionStep->name}を{$resultLabel}としました。");
    }

    /**
     * 応募ステータス変更
     */
    public function updateStatus(Request $request, Application $application)
    {
        $companyId = $request->attributes->get('company_id');
        $userId = $request->attributes->get('auth_user_id');

        if ($application->candidate->company_id !== $companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:active,offered,hired,rejected,withdrawn'],
        ]);

        $application->update([
            'status' => $validated['status'],
        ]);

        $statusLabels = [
            'active' => '選考中',
            'offered' => '内定',
            'hired' => '入社',
            'rejected' => '不採用',
            'withdrawn' => '辞退',
        ];

        return back()->with('success', "ステータスを「{$statusLabels[$validated['status']]}」に変更しました。");
    }

    /**
     * 面接日時を設定
     */
    public function scheduleStep(Request $request, Application $application, int $stepId)
    {
        $companyId = $request->attributes->get('company_id');
        $userId = $request->attributes->get('auth_user_id');

        if ($application->candidate->company_id !== $companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $selectionStep = $application->job->selectionSteps()
            ->where('id', $stepId)
            ->firstOrFail();

        ApplicationStep::updateOrCreate(
            [
                'application_id' => $application->id,
                'selection_step_id' => $selectionStep->id,
            ],
            [
                'scheduled_at' => $validated['scheduled_at'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return back()->with('success', '面接日時を設定しました。');
    }
}
