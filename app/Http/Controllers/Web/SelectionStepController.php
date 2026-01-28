<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\SelectionStep;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SelectionStepController extends Controller
{
    /**
     * 選考ステップ設定画面
     */
    public function index(Request $request, Job $job): Response
    {
        $user = $request->user();

        if ($job->company_id !== $user->company_id) {
            abort(403);
        }

        $steps = $job->selectionSteps()->orderBy('order')->get();

        return Inertia::render('Jobs/SelectionSteps/Index', [
            'job' => $job,
            'steps' => $steps,
        ]);
    }

    /**
     * 選考ステップ保存（一括更新）
     */
    public function store(Request $request, Job $job)
    {
        $user = $request->user();

        if ($job->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'steps' => ['required', 'array'],
            'steps.*.id' => ['nullable', 'integer'],
            'steps.*.name' => ['required', 'string', 'max:255'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.duration_days' => ['nullable', 'integer', 'min:1'],
            'steps.*.is_active' => ['boolean'],
        ]);

        $existingIds = [];

        foreach ($validated['steps'] as $index => $stepData) {
            if (!empty($stepData['id'])) {
                // 既存ステップの更新
                $step = SelectionStep::where('id', $stepData['id'])
                    ->where('job_id', $job->id)
                    ->first();

                if ($step) {
                    $step->update([
                        'name' => $stepData['name'],
                        'description' => $stepData['description'] ?? null,
                        'duration_days' => $stepData['duration_days'] ?? null,
                        'is_active' => $stepData['is_active'] ?? true,
                        'order' => $index,
                    ]);
                    $existingIds[] = $step->id;
                }
            } else {
                // 新規ステップ作成
                $step = SelectionStep::create([
                    'job_id' => $job->id,
                    'name' => $stepData['name'],
                    'description' => $stepData['description'] ?? null,
                    'duration_days' => $stepData['duration_days'] ?? null,
                    'is_active' => $stepData['is_active'] ?? true,
                    'order' => $index,
                ]);
                $existingIds[] = $step->id;
            }
        }

        // 削除されたステップを削除（応募進捗がないもののみ）
        SelectionStep::where('job_id', $job->id)
            ->whereNotIn('id', $existingIds)
            ->whereDoesntHave('applicationSteps')
            ->delete();

        return back()->with('success', '選考ステップを保存しました。');
    }

    /**
     * デフォルトテンプレートを適用
     */
    public function applyTemplate(Request $request, Job $job)
    {
        $user = $request->user();

        if ($job->company_id !== $user->company_id) {
            abort(403);
        }

        // 既存ステップがあれば何もしない
        if ($job->selectionSteps()->exists()) {
            return back()->with('error', '既に選考ステップが設定されています。');
        }

        $defaultSteps = [
            ['name' => '書類選考', 'description' => '履歴書・職務経歴書の確認', 'duration_days' => 3],
            ['name' => '一次面接', 'description' => '人事担当者による面接', 'duration_days' => 7],
            ['name' => '二次面接', 'description' => '部門責任者による面接', 'duration_days' => 7],
            ['name' => '最終面接', 'description' => '役員面接', 'duration_days' => 7],
            ['name' => '内定', 'description' => '内定通知・条件提示', 'duration_days' => 3],
        ];

        foreach ($defaultSteps as $index => $stepData) {
            SelectionStep::create([
                'job_id' => $job->id,
                'name' => $stepData['name'],
                'description' => $stepData['description'],
                'duration_days' => $stepData['duration_days'],
                'order' => $index,
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'デフォルトテンプレートを適用しました。');
    }
}
