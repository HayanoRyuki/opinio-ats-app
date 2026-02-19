<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    /**
     * ページ作成フォーム
     */
    public function create(Request $request, Job $job): Response
    {
        $companyId = $request->attributes->get('company_id');

        if ($job->company_id !== $companyId) {
            abort(403);
        }

        return Inertia::render('Jobs/Pages/Create', [
            'job' => $job,
        ]);
    }

    /**
     * ページ保存
     */
    public function store(Request $request, Job $job)
    {
        $companyId = $request->attributes->get('company_id');

        if ($job->company_id !== $companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug', 'regex:/^[a-z0-9\-]+$/'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $page = $job->pages()->create($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', '求人ページを作成しました。');
    }

    /**
     * ページ編集フォーム
     */
    public function edit(Request $request, Job $job, Page $page): Response
    {
        $companyId = $request->attributes->get('company_id');

        if ($job->company_id !== $companyId) {
            abort(403);
        }

        if ($page->job_id !== $job->id) {
            abort(404);
        }

        return Inertia::render('Jobs/Pages/Edit', [
            'job' => $job,
            'page' => $page,
        ]);
    }

    /**
     * ページ更新
     */
    public function update(Request $request, Job $job, Page $page)
    {
        $companyId = $request->attributes->get('company_id');

        if ($job->company_id !== $companyId) {
            abort(403);
        }

        if ($page->job_id !== $job->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug,' . $page->id, 'regex:/^[a-z0-9\-]+$/'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $page->update($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', '求人ページを更新しました。');
    }

    /**
     * ページ削除
     */
    public function destroy(Request $request, Job $job, Page $page)
    {
        $companyId = $request->attributes->get('company_id');

        if ($job->company_id !== $companyId) {
            abort(403);
        }

        if ($page->job_id !== $job->id) {
            abort(404);
        }

        $page->delete();

        return redirect()->route('jobs.show', $job)
            ->with('success', '求人ページを削除しました。');
    }

    /**
     * ステータス変更（公開/下書き切り替え）
     */
    public function updateStatus(Request $request, Job $job, Page $page)
    {
        $companyId = $request->attributes->get('company_id');

        if ($job->company_id !== $companyId) {
            abort(403);
        }

        if ($page->job_id !== $job->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:draft,published'],
        ]);

        $page->update(['status' => $validated['status']]);

        $label = $validated['status'] === 'published' ? '公開' : '下書き';

        return back()->with('success', "ページを「{$label}」に変更しました。");
    }
}
