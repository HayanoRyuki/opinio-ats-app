<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->attributes->get('role');

        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        $applications = Application::with('candidate')
            ->latest()
            ->get();

        return view('candidates.index', [
            'applications' => $applications,
        ]);
    }

    public function show(Request $request, $id)
    {
        $role   = $request->attributes->get('role');
        $userId = $request->attributes->get('user_id');

        if ($role === 'interviewer' && ! $this->isAssigned($userId, $id)) {
            abort(403, 'アクセス権限がありません。');
        }

        $applications = Application::with(['candidate', 'job'])
            ->whereHas('candidate', fn ($q) => $q->where('id', $id))
            ->latest()
            ->get();

        $candidate = optional($applications->first())->candidate;

        if (! $candidate) {
            abort(404);
        }

        return view('candidates.show', [
            'candidate'    => $candidate,
            'applications' => $applications,
        ]);
    }

    private function isAssigned($userId, $candidateId)
    {
        return true;
    }
}
