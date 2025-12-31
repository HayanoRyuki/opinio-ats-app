@extends('layouts.ats')

@section('content')
<div style="max-width:960px; margin:0 auto;">

    <h1 style="font-size:1.5rem; font-weight:600; margin-bottom:24px;">
        新規求人作成
    </h1>

    @if ($errors->any())
        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:6px; margin-bottom:20px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('jobs.store') }}"
          style="background:#fff; padding:32px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.05);">
        @csrf

        {{-- ===== 基本情報（2カラム） ===== --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <div>
                <label>タイトル</label>
                <input type="text" name="title" value="{{ old('title') }}" required>
            </div>

            <div>
                <label>勤務地</label>
                <input type="text" name="location" value="{{ old('location') }}">
            </div>

            <div>
                <label>雇用形態</label>
                <select name="employment_type">
                    <option value="">選択してください</option>
                    <option value="full_time" @selected(old('employment_type')=='full_time')>正社員</option>
                    <option value="part_time" @selected(old('employment_type')=='part_time')>アルバイト・パート</option>
                    <option value="contract" @selected(old('employment_type')=='contract')>契約社員</option>
                    <option value="internship" @selected(old('employment_type')=='internship')>インターン</option>
                </select>
            </div>

            <div>
                <label>給与</label>
                <input type="text" name="salary" value="{{ old('salary') }}">
            </div>

            <div>
                <label>勤務時間</label>
                <input type="text" name="working_hours" value="{{ old('working_hours') }}">
            </div>

        </div>

        {{-- ===== 詳細情報（1カラム） ===== --}}
        <div style="margin-top:32px; display:flex; flex-direction:column; gap:20px;">

            <div>
                <label>仕事内容</label>
                <textarea name="description" rows="5">{{ old('description') }}</textarea>
            </div>

            <div>
                <label>応募資格・条件</label>
                <textarea name="requirements" rows="3">{{ old('requirements') }}</textarea>
            </div>

            <div>
                <label>待遇・福利厚生</label>
                <textarea name="benefits" rows="3">{{ old('benefits') }}</textarea>
            </div>

            <div>
                <label>備考</label>
                <textarea name="notes" rows="2">{{ old('notes') }}</textarea>
            </div>

        </div>

        {{-- ===== アクション ===== --}}
        <div style="margin-top:32px; text-align:right;">
            <button type="submit"
                style="background:#111827; color:#fff; padding:12px 24px; border-radius:8px; border:none; font-weight:600;">
                作成する
            </button>
        </div>
    </form>
</div>

{{-- ===== フォーム共通スタイル ===== --}}
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
