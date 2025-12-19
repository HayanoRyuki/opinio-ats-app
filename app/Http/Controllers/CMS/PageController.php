<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Page;
use App\Models\Job;

class PageController extends Controller
{
    /**
     * CMS ページ一覧
     */
    public function index()
    {
        $pages = Page::with('job')
            ->orderByDesc('created_at')
            ->get();

        return view('cms.pages.index', compact('pages'));
    }

    /**
     * CMS ページ作成画面
     * ATS から渡された job_id を受け取る
     */
    public function create(Request $request)
    {
        $jobs = Job::orderBy('title')->get();

        // ?job_id=xx があれば自動選択
        $selectedJobId = $request->query('job_id');

        return view('cms.pages.create', [
            'jobs' => $jobs,
            'selectedJobId' => $selectedJobId,
        ]);
    }

    /**
     * CMS ページ保存（新規）
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id'  => ['nullable', 'exists:jobs,id'],
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'status'  => ['required', 'in:draft,published'],
        ]);

        // slug 生成
        $validated['slug'] = Str::slug($validated['title']);

        // slug 重複回避（最小実装）
        if (Page::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-' . time();
        }

        Page::create($validated);

        return redirect()
            ->route('cms.pages.index')
            ->with('success', 'ページを保存しました');
    }

    /**
     * CMS ページ編集画面
     */
    public function edit(Page $page)
    {
        $jobs = Job::orderBy('title')->get();

        return view('cms.pages.edit', compact('page', 'jobs'));
    }

    /**
     * CMS ページ更新
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'job_id'  => ['nullable', 'exists:jobs,id'],
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'status'  => ['required', 'in:draft,published'],
        ]);

        // タイトルが変わった場合のみ slug 再生成
        if ($validated['title'] !== $page->title) {
            $slug = Str::slug($validated['title']);

            if (
                Page::where('slug', $slug)
                    ->where('id', '!=', $page->id)
                    ->exists()
            ) {
                $slug .= '-' . time();
            }

            $validated['slug'] = $slug;
        }

        $page->update($validated);

        return redirect()
            ->route('cms.pages.index')
            ->with('success', 'ページを更新しました');
    }

    /**
     * CMS ページ削除
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()
            ->route('cms.pages.index')
            ->with('success', 'ページを削除しました');
    }
}
