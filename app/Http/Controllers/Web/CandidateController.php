<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CandidateController extends Controller
{
    /**
     * 候補者一覧
     */
    public function index(Request $request): Response
    {
        $companyId = $request->attributes->get('company_id');

        $query = Candidate::where('company_id', $companyId)
            ->with(['person', 'applications.job']);

        // 検索
        if ($search = $request->input('search')) {
            $query->whereHas('person', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // チャネルフィルタ
        if ($channel = $request->input('channel')) {
            $query->where('source_channel', $channel);
        }

        $candidates = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // 統計
        $stats = [
            'total' => Candidate::where('company_id', $companyId)->count(),
            'direct' => Candidate::where('company_id', $companyId)->where('source_channel', 'direct')->count(),
            'media' => Candidate::where('company_id', $companyId)->where('source_channel', 'media')->count(),
            'agent' => Candidate::where('company_id', $companyId)->where('source_channel', 'agent')->count(),
            'referral' => Candidate::where('company_id', $companyId)->where('source_channel', 'referral')->count(),
        ];

        return Inertia::render('Candidates/Index', [
            'candidates' => $candidates,
            'stats' => $stats,
            'filters' => [
                'search' => $request->input('search'),
                'channel' => $request->input('channel'),
            ],
        ]);
    }

    /**
     * 候補者詳細
     */
    public function show(Request $request, Candidate $candidate): Response
    {
        $companyId = $request->attributes->get('company_id');

        // 自社の候補者のみアクセス可能
        if ($candidate->company_id !== $companyId) {
            abort(403);
        }

        $candidate->load(['person', 'applications.job']);

        return Inertia::render('Candidates/Show', [
            'candidate' => $candidate,
        ]);
    }
}
