@extends('layouts.ats')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">職種を編集</h1>

    <form method="POST"
          action="{{ route('ats.job_roles.update', $jobRole) }}">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label class="block font-semibold mb-1">
                表示名 <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="display_name"
                   value="{{ old('display_name', $jobRole->display_name) }}"
                   class="w-full border rounded p-2"
                   required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">
                内部名
            </label>
            <input type="text"
                   name="internal_name"
                   value="{{ old('internal_name', $jobRole->internal_name) }}"
                   class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">
                説明
            </label>
            <textarea name="description"
                      class="w-full border rounded p-2"
                      rows="4">{{ old('description', $jobRole->description) }}</textarea>
        </div>

        <div class="mb-6">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       {{ $jobRole->is_active ? 'checked' : '' }}>
                <span>有効</span>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded">
                更新する
            </button>

            <a href="{{ route('ats.job_roles.index') }}"
               class="text-gray-600">
                戻る
            </a>
        </div>
    </form>
</div>
@endsection
