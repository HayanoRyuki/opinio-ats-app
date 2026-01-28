<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Enums\JobStatus;
use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{
    /**
     * 求人一覧
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = Job::where('company_id', $user->company_id)
            ->withCount('applications');

        // ステータスフィルタ
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // 検索
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $jobs = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // 統計
        $stats = [
            'total' => Job::where('company_id', $user->company_id)->count(),
            'open' => Job::where('company_id', $user->company_id)->where('status', 'open')->count(),
            'draft' => Job::where('company_id', $user->company_id)->where('status', 'draft')->count(),
            'closed' => Job::where('company_id', $user->company_id)->where('status', 'closed')->count(),
        ];

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
            'stats' => $stats,
            'filters' => [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ],
        ]);
    }

    /**
     * 求人作成フォーム
     */
    public function create(): Response
    {
        return Inertia::render('Jobs/Create');
    }

    /**
     * 求人保存
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,intern'],
            'location' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,open,paused,closed'],
        ]);

        $job = Job::create([
            'company_id' => $user->company_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'employment_type' => $validated['employment_type'],
            'location' => $validated['location'] ?? null,
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'open' ? now() : null,
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', '求人を作成しました。');
    }

    /**
     * 求人詳細
     */
    public function show(Request $request, Job $job): Response
    {
        $user = $request->user();

        if ($job->company_id !== $user->company_id) {
            abort(403);
        }

        $job->load(['applications.candidate.person']);
        $job->loadCount('applications');

        return Inertia::render('Jobs/Show', [
            'job' => $job,
        ]);
    }

    /**
     * 求人編集フォーム
     */
    public function edit(Request $request, Job $job): Response
    {
        $user = $request->user();

        if ($job->company_id !== $user->company_id) {
            abort(403);
        }

        return Inertia::render('Jobs/Edit', [
            'job' => $job,
        ]);
    }

    /**
     * 求人更新
     */
    public function update(Request $request, Job $job)
    {
        $user = $request->user();

        if ($job->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,intern'],
            'location' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,open,paused,closed'],
        ]);

        // 初めて公開する場合は published_at を設定
        if ($validated['status'] === 'open' && !$job->published_at) {
            $validated['published_at'] = now();
        }

        // 募集終了時は closed_at を設定
        if ($validated['status'] === 'closed' && !$job->closed_at) {
            $validated['closed_at'] = now();
        }

        $job->update($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', '求人を更新しました。');
    }

    /**
     * ステータス変更（クイックアクション）
     */
    public function updateStatus(Request $request, Job $job)
    {
        $user = $request->user();

        if ($job->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:draft,open,paused,closed'],
        ]);

        $updates = ['status' => $validated['status']];

        if ($validated['status'] === 'open' && !$job->published_at) {
            $updates['published_at'] = now();
        }

        if ($validated['status'] === 'closed' && !$job->closed_at) {
            $updates['closed_at'] = now();
        }

        $job->update($updates);

        $statusLabels = [
            'draft' => '下書き',
            'open' => '募集中',
            'paused' => '一時停止',
            'closed' => '募集終了',
        ];

        return back()->with('success', "ステータスを「{$statusLabels[$validated['status']]}」に変更しました。");
    }
}
