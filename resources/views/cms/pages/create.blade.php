@extends('layouts.menu')

@section('content')
<div class="max-w-4xl mx-auto p-6">

    {{-- ヘッダー --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold mb-2">CMS ページ作成</h1>
        <p class="text-sm text-gray-600">
            求人に紐づく公開用ページを作成します。下書き保存後に内容を調整し、公開できます。
        </p>
    </div>

    <form method="POST" action="{{ route('cms.pages.store') }}">
        @csrf

        <div class="bg-white border rounded-lg p-6">

            {{-- 紐づく求人 --}}
            <div class="mb-6">
                <label class="block mb-1 font-medium">
                    紐づく求人 <span class="text-sm text-gray-500">（任意）</span>
                </label>
                <p class="text-xs text-gray-500 mb-2">
                    ATSで管理している求人とページを紐づけると、一覧や管理がしやすくなります。
                </p>
                <select name="job_id" class="w-full border rounded-md p-2">
                    <option value="">選択しない</option>
                    @foreach ($jobs as $job)
                        <option value="{{ $job->id }}"
                            @selected(old('job_id', $selectedJobId) == $job->id)>
                            {{ $job->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- タイトル --}}
            <div class="mb-6">
                <label class="block mb-1 font-medium">タイトル</label>
                <input type="text"
                       name="title"
                       class="w-full border rounded-md p-2"
                       value="{{ old('title') }}"
                       placeholder="例：サーバーサイドエンジニア募集要項"
                       required>
            </div>

            {{-- 本文 --}}
            <div class="mb-6">
                <label class="block mb-1 font-medium">本文</label>
                <textarea name="content"
                          rows="10"
                          class="w-full border rounded-md p-2"
                          placeholder="仕事内容、求める人物像、働く環境などを記載します。">{{ old('content') }}</textarea>
            </div>

            {{-- ステータス --}}
            <div class="mb-8">
                <label class="block mb-1 font-medium">ステータス</label>
                <select name="status" class="border rounded-md p-2">
                    <option value="draft" @selected(old('status') === 'draft')>
                        下書き（非公開）
                    </option>
                    <option value="published" @selected(old('status') === 'published')>
                        公開
                    </option>
                </select>
                <p class="text-xs text-gray-500 mt-2">
                    ※ 下書きの場合、外部には公開されません。
                </p>
            </div>

            {{-- 操作 --}}
            <div class="flex justify-end gap-4">
    <a href="{{ route('cms.pages.index') }}"
       class="text-sm text-gray-600 hover:text-gray-900 self-center">
        キャンセル
    </a>

    <button type="submit"
            class="btn-primary px-8 py-3 text-base font-semibold">
        保存
    </button>
</div>

        </div>
    </form>
</div>
@endsection
