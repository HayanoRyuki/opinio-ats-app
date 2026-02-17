<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Recommendation;
use App\Services\RecommendationLinkService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecommendationController extends Controller
{
    public function __construct(
        private RecommendationLinkService $linkService
    ) {}

    /**
     * 推薦一覧
     */
    public function index(Request $request): Response
    {
        $companyId = $request->attributes->get('company_id');
        $status = $request->query('status');
        $agentId = $request->query('agent_id');
        $sort = $request->query('sort', 'newest');

        $query = Recommendation::where('company_id', $companyId)
            ->with(['agent', 'job', 'links.candidate.person']);

        // ステータスフィルター
        if ($status) {
            $query->where('status', $status);
        }

        // エージェントフィルター
        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        // ソート
        if ($sort === 'oldest') {
            $query->orderBy('received_at', 'asc');
        } else {
            $query->orderBy('received_at', 'desc');
        }

        $recommendations = $query->paginate(20)->withQueryString();

        // フィルター用エージェント一覧
        $agents = Agent::forCompany($companyId)
            ->active()
            ->orderBy('organization')
            ->get(['id', 'name', 'organization']);

        // 統計
        $stats = [
            'total' => Recommendation::where('company_id', $companyId)->count(),
            'pending' => Recommendation::where('company_id', $companyId)
                ->whereIn('status', ['pending', 'received', 'processing', 'draft'])
                ->count(),
            'confirmed' => Recommendation::where('company_id', $companyId)
                ->where('status', 'confirmed')
                ->count(),
            'rejected' => Recommendation::where('company_id', $companyId)
                ->where('status', 'rejected')
                ->count(),
        ];

        return Inertia::render('Recommendations/Index', [
            'recommendations' => $recommendations,
            'agents' => $agents,
            'filters' => [
                'status' => $status,
                'agent_id' => $agentId,
                'sort' => $sort,
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * 推薦詳細（原文含む）
     */
    public function show(Request $request, Recommendation $recommendation): Response
    {
        $companyId = $request->attributes->get('company_id');

        $recommendation->load([
            'agent',
            'job',
            'applicationIntake',
            'links.candidate.person',
            'links.application',
        ]);

        // 紐付け候補の候補者検索
        $candidateSuggestions = $this->linkService
            ->searchCandidatesForLinking($recommendation, $companyId);

        // 重複推薦検出
        $duplicateRecommendations = $this->linkService
            ->findDuplicateRecommendations($recommendation);

        return Inertia::render('Recommendations/Show', [
            'recommendation' => $recommendation,
            'candidateSuggestions' => $candidateSuggestions,
            'duplicateRecommendations' => $duplicateRecommendations,
        ]);
    }

    /**
     * 候補者紐付け
     */
    public function link(Request $request, Recommendation $recommendation)
    {
        $validated = $request->validate([
            'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
            'application_id' => ['nullable', 'integer', 'exists:applications,id'],
        ]);

        $this->linkService->link(
            $recommendation,
            $validated['candidate_id'],
            $validated['application_id'] ?? null
        );

        return redirect()->back()
            ->with('success', '候補者を紐付けました。');
    }

    /**
     * 紐付け解除
     */
    public function unlink(Request $request, Recommendation $recommendation)
    {
        $validated = $request->validate([
            'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
        ]);

        $this->linkService->unlink($recommendation, $validated['candidate_id']);

        return redirect()->back()
            ->with('success', '紐付けを解除しました。');
    }

    /**
     * アーカイブ（非表示・保管）
     */
    public function archive(Request $request, Recommendation $recommendation)
    {
        $recommendation->update([
            'status' => 'rejected', // アーカイブ ≒ rejected として扱う
        ]);

        return redirect()->route('recommendations.index')
            ->with('success', '推薦をアーカイブしました。');
    }
}
