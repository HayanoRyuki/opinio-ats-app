<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    /**
     * エージェント一覧
     */
    public function index(Request $request): Response
    {
        $companyId = $request->attributes->get('company_id');
        $search = $request->query('search');
        $status = $request->query('status'); // active, inactive, all

        $query = Agent::forCompany($companyId)
            ->withCount('recommendations');

        // 検索
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ステータスフィルター
        if ($status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($status !== 'all') {
            $query->active();
        }

        $agents = $query->orderBy('organization')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        // 統計
        $stats = [
            'total' => Agent::forCompany($companyId)->count(),
            'active' => Agent::forCompany($companyId)->active()->count(),
        ];

        return Inertia::render('Agents/Index', [
            'agents' => $agents,
            'filters' => [
                'search' => $search,
                'status' => $status ?? 'active',
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * エージェント新規作成フォーム
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Agents/Create');
    }

    /**
     * エージェント保存
     */
    public function store(Request $request)
    {
        $companyId = $request->attributes->get('company_id');

        $validated = $request->validate([
            'organization' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'agent_type' => ['required', 'in:human,ai'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $agent = Agent::create([
            ...$validated,
            'company_id' => $companyId,
        ]);

        return redirect()->route('agents.show', $agent)
            ->with('success', "エージェント「{$agent->display_name}」を登録しました。");
    }

    /**
     * エージェント詳細（推薦履歴含む）
     */
    public function show(Request $request, Agent $agent): Response
    {
        $agent->loadCount('recommendations');

        $recommendations = $agent->recommendations()
            ->with(['job', 'links.candidate'])
            ->orderBy('received_at', 'desc')
            ->paginate(10);

        // 推薦の統計
        $recommendationStats = [
            'total' => $agent->recommendations()->count(),
            'confirmed' => $agent->recommendations()->where('status', 'confirmed')->count(),
            'rejected' => $agent->recommendations()->where('status', 'rejected')->count(),
            'pending' => $agent->recommendations()->whereIn('status', ['pending', 'received', 'processing', 'draft'])->count(),
        ];

        return Inertia::render('Agents/Show', [
            'agent' => $agent,
            'recommendations' => $recommendations,
            'recommendationStats' => $recommendationStats,
        ]);
    }

    /**
     * エージェント編集フォーム
     */
    public function edit(Request $request, Agent $agent): Response
    {
        return Inertia::render('Agents/Edit', [
            'agent' => $agent,
        ]);
    }

    /**
     * エージェント更新
     */
    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'organization' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'agent_type' => ['required', 'in:human,ai'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
        ]);

        $agent->update($validated);

        return redirect()->route('agents.show', $agent)
            ->with('success', 'エージェント情報を更新しました。');
    }
}
