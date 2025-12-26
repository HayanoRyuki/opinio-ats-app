<?php

namespace App\Http\Controllers\Decide;

use App\Http\Controllers\Controller;
use App\Application\Decide\MakeHiringDecisionUseCase;
use Illuminate\Http\Request;
use App\Models\Application;

final class HiringDecisionController extends Controller
{
    public function __construct(
        private MakeHiringDecisionUseCase $useCase
    ) {}

    public function store(Request $request, Application $application)
    {
        $validated = $request->validate([
            'decision' => 'required|in:hire,reject,hold',
            'reason'   => 'nullable|string',
        ]);

        $this->useCase->execute(
            applicationId: $application->id,
            decision: $validated['decision'],
            decidedBy: $request->user()->id,
            reason: $validated['reason'] ?? null,
        );

        return back();
    }
}