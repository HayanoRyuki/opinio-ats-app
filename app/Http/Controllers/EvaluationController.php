<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    /**
     * 評価入力画面
     */
    public function create(Application $application)
    {
        $application->load([
            'candidate',
            'job',
            'selectionStep',
        ]);

        return view('evaluations.create', [
            'application' => $application,
        ]);
    }

    /**
     * 評価保存
     */
    public function store(Request $request, Application $application)
    {
        $data = $request->validate([
            'overall_score' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
        ]);

        Evaluation::create([
            'application_id' => $application->id,
            'user_id' => Auth::id() ?? 1, // ★ 仮ログイン対応
            'step_key' => $application->selectionStep->key,
            'overall_score' => $data['overall_score'],
            'comment' => $data['comment'] ?? null,
            'recommendation' => $data['recommendation'] ?? null,
        ]);

        return redirect()
            ->route('jobs.pipeline', $application->job)
            ->with('status', '評価を保存しました');
    }
}
