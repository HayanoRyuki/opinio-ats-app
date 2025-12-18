@extends('layouts.menu')

@section('content')
<div style="max-width:960px; margin:0 auto;">

    {{-- ヘッダー --}}
    <h1 style="font-size:1.5rem; font-weight:600; margin-bottom:24px;">
        会社情報編集
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

    <form method="POST"
          action="{{ route('company.update') }}"
          style="background:#fff; padding:32px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.05);">
        @csrf
        @method('PATCH')

        {{-- 基本情報 --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <div>
                <label>会社名</label>
                <input type="text" name="name"
                       value="{{ old('name', $company->name) }}" required>
            </div>

            <div>
                <label>メールドメイン</label>
                <input type="text" name="domain"
                       value="{{ old('domain', $company->domain) }}" required>
            </div>

            <div>
                <label>業種</label>
                <input type="text" name="industry"
                       value="{{ old('industry', $company->industry) }}">
            </div>

            <div>
                <label>電話番号</label>
                <input type="text" name="phone"
                       value="{{ old('phone', $company->phone) }}">
            </div>

        </div>

        {{-- 補足情報 --}}
        <div style="margin-top:24px; display:flex; flex-direction:column; gap:20px;">
            <div>
                <label>住所</label>
                <input type="text" name="address"
                       value="{{ old('address', $company->address) }}">
            </div>

            <div>
                <label>ウェブサイト</label>
                <input type="url" name="website"
                       value="{{ old('website', $company->website) }}">
            </div>
        </div>

        {{-- アクション --}}
        <div style="margin-top:32px; display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('recruiter.mypage') }}"
               style="color:#6b7280; text-decoration:none;">
                ← マイページに戻る
            </a>

            <button type="submit"
                style="background:#111827; color:#fff; padding:12px 24px; border-radius:8px; border:none; font-weight:600;">
                更新する
            </button>
        </div>
    </form>
</div>

{{-- 共通フォームスタイル --}}
<style>
label {
    display:block;
    font-size:0.85rem;
    font-weight:600;
    margin-bottom:6px;
    color:#374151;
}

input {
    width:100%;
    padding:10px 12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    font-size:0.9rem;
}

input:focus {
    outline:none;
    border-color:#2563eb;
}
</style>
@endsection
