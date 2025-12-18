@extends('layouts.menu')

@section('content')
<div style="max-width:960px; margin:0 auto;">

    <h1 style="font-size:1.5rem; font-weight:600; margin-bottom:8px;">
        求人編集
    </h1>
    <p style="color:#6b7280; margin-bottom:24px;">
        {{ $job->title }}
    </p>

    @if ($errors->any())
        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:6px; margin-bottom:20px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('jobs.update', $job) }}"
          style="background:#fff; padding:32px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.05);">
        @csrf
        @method('PATCH')

        {{-- ===== 基本情報（2カラム） ===== --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <div>
                <label>タイトル</label>
                <input type="text" name="title" value="{{ old('title', $job->title) }}" required>
            </div>

            <div>
                <label>勤務地</label>
                <input type="text" name="location" value="{{ old('location', $job->location) }}">
            </div>

            <div>
                <label>雇用形態</label>
                <select name="employment_type">
                    <option value="">選択してください</option>
                    <option value="full_time" @selected(old('employment_type', $job->employment_type)==='full_time')>正社員</option>
                    <option value="part_time" @selected(old('employment_type', $job->employment_type)==='part_time')>アルバイト・パート</option>
                    <option value="contract" @selected(old('employment_type', $job->employment_type)==='contract')>契約社員</option>
                    <option value="internship" @selected(old('employment_type', $job->employment_type)==='internship')>インターン</option>
                </select>
            </div>

            <div>
                <label>給与</label>
                <input type="text" name="salary" value="{{ old('salary', $job->salary) }}">
            </div>

            <div>
                <label>勤務時間</label>
                <input type="text" name="working_hours" value="{{ old('working_hours', $job->working_hours) }}">
            </div>

        </div>

        {{-- ===== 詳細情報（1カラム） ===== --}}
        <div style="margin-top:32px; display:flex; flex-direction:column; gap:20px;">

            <div>
                <label>仕事内容</label>
                <textarea name="description" rows="5">{{ old('description', $job->description) }}</textarea>
            </div>

            <div>
                <label>応募資格・条件</label>
                <textarea name="requirements" rows="3">{{ old('requirements', $job->requirements) }}</textarea>
            </div>

            <div>
                <label>待遇・福利厚生</label>
                <textarea name="benefits" rows="3">{{ old('benefits', $job->benefits) }}</textarea>
            </div>

            <div>
                <label>備考</label>
                <textarea name="notes" rows="2">{{ old('notes', $job->notes) }}</textarea>
            </div>

        </div>

        {{-- ===== アクション ===== --}}
        <div style="margin-top:32px; display:flex; justify-content:space-between; align-items:center;">

            <a href="{{ route('jobs.index') }}"
               style="color:#6b7280; text-decoration:none;">
                ← 求人一覧に戻る
            </a>

            <button type="submit"
                style="background:#111827; color:#fff; padding:12px 24px; border-radius:8px; border:none; font-weight:600;">
                更新する
            </button>
        </div>
    </form>
</div>

{{-- ===== フォーム共通スタイル（createと共通） ===== --}}
<style>
label {
    display:block;
    font-size:0.85rem;
    font-weight:600;
    margin-bottom:6px;
    color:#374151;
}

input, select, textarea {
    width:100%;
    padding:10px 12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    font-size:0.9rem;
}

input:focus, textarea:focus, select:focus {
    outline:none;
    border-color:#2563eb;
}
</style>
@endsection
