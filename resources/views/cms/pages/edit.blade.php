@extends('layouts.ats')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">CMS ページ編集</h1>

    <form method="POST" action="{{ route('cms.pages.update', $page) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block mb-1">紐づく求人（任意）</label>
            <select name="job_id" class="w-full border p-2">
                <option value="">選択しない</option>
                @foreach ($jobs as $job)
                    <option value="{{ $job->id }}"
                        @selected($page->job_id === $job->id)>
                        {{ $job->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1">タイトル</label>
            <input type="text" name="title"
                value="{{ old('title', $page->title) }}"
                class="w-full border p-2" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1">本文</label>
            <textarea name="content" rows="10"
                class="w-full border p-2">{{ old('content', $page->content) }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block mb-1">ステータス</label>
            <select name="status" class="border p-2">
                <option value="draft" @selected($page->status === 'draft')>下書き</option>
                <option value="published" @selected($page->status === 'published')>公開</option>
            </select>
        </div>

        <div class="flex justify-between">
            <button type="submit" class="btn-primary">
                更新
            </button>

            <form method="POST"
                action="{{ route('cms.pages.destroy', $page) }}"
                onsubmit="return confirm('本当に削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600">
                    削除
                </button>
            </form>
        </div>
    </form>
</div>
@endsection
