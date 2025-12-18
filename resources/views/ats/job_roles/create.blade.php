@extends('layouts.menu')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">職種を追加</h1>

    <form method="POST" action="{{ route('ats.job_roles.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block font-semibold mb-1">
                表示名 <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="display_name"
                   value="{{ old('display_name') }}"
                   class="w-full border rounded p-2"
                   required>
            <p class="text-sm text-gray-500 mt-1">
                求人や画面に表示される名前です。
            </p>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">
                内部名（任意）
            </label>
            <input type="text"
                   name="internal_name"
                   value="{{ old('internal_name') }}"
                   class="w-full border rounded p-2">
            <p class="text-sm text-gray-500 mt-1">
                社内管理・AI処理用の名称です（未入力の場合は表示名を使用します）。
            </p>
        </div>

        <div class="mb-6">
            <label class="block font-semibold mb-1">
                説明（任意）
            </label>
            <textarea name="description"
                      class="w-full border rounded p-2"
                      rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded">
                登録する
            </button>

            <a href="{{ route('ats.job_roles.index') }}"
               class="text-gray-600">
                キャンセル
            </a>
        </div>
    </form>
</div>
@endsection
