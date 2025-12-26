<?php

namespace App\Http\Controllers;

use App\Application\Decide\MakeHiringDecisionUseCase;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

final class HiringDecisionController extends Controller
{
    public function __construct(
        private MakeHiringDecisionUseCase $useCase
    ) {}

    /**
     * 採用判断を確定する
     */
    public function store(
        Request $request,
        Application $application
    ): RedirectResponse {
        $validated = $request->validate([
            'decision' => 'required|in:hire,reject,hold',
            'reason'   => 'nullable|string',
        ]);

        $this->useCase->execute(
            applicationId: $application->id,
            decidedBy: $request->user()->id,
            decision: $validated['decision'],
            reason: $validated['reason'] ?? null,
        );

        return back()->with('status', '採用判断を保存しました');
    }
}
