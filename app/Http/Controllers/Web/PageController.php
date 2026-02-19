<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'featured_image' => ['nullable', 'image', 'max:5120'], // 5MB
            'status' => ['required', 'in:draft,published'],
        ]);

        // 画像アップロード処理
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('pages/featured', 'public');
        }

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
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,published'],
        ]);

        // 画像削除リクエスト
        if ($request->boolean('remove_featured_image')) {
            if ($page->featured_image) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $validated['featured_image'] = null;
        }
        // 新しい画像アップロード
        elseif ($request->hasFile('featured_image')) {
            // 古い画像を削除
            if ($page->featured_image) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('pages/featured', 'public');
        } else {
            // 画像の変更なし → featured_image をバリデーション結果から除外
            unset($validated['featured_image']);
        }

        unset($validated['remove_featured_image']);
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

        // 画像も削除
        if ($page->featured_image) {
            Storage::disk('public')->delete($page->featured_image);
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
