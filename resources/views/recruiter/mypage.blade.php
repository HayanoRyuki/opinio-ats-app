@extends('layouts.menu')

@section('content')
<div style="max-width:960px; margin:0 auto;">

    {{-- ページタイトル --}}
    <h1 style="font-size:1.5rem; font-weight:600; margin-bottom:24px;">
        マイページ
    </h1>

    {{-- 会社情報カード --}}
    <div style="
        background:#ffffff;
        border-radius:12px;
        padding:24px;
        margin-bottom:32px;
        box-shadow:0 10px 30px rgba(0,0,0,0.05);
    ">
        <h2 style="font-size:1.1rem; font-weight:600; margin-bottom:16px;">
            会社情報
        </h2>

        <div style="margin-bottom:8px;">
            <strong>会社名：</strong>{{ $company?->name ?? '-' }}
        </div>
        <div style="margin-bottom:16px;">
            <strong>ドメイン：</strong>{{ $company?->domain ?? '-' }}
        </div>

        <a href="{{ route('company.edit') }}"
           style="
               display:inline-block;
               padding:8px 16px;
               background:#2563eb;
               color:#fff;
               border-radius:6px;
               text-decoration:none;
               font-size:0.9rem;
               font-weight:600;
           ">
            会社情報を編集
        </a>
    </div>

    {{-- 社員一覧 --}}
    <div style="
        background:#ffffff;
        border-radius:12px;
        padding:24px;
        box-shadow:0 10px 30px rgba(0,0,0,0.05);
    ">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:1.1rem; font-weight:600;">
                自社社員一覧
            </h2>

            {{-- 将来拡張用 --}}
            <span style="font-size:0.8rem; color:#9ca3af;">
                ※ 招待機能は準備中
            </span>
        </div>

        <table style="
            width:100%;
            border-collapse:collapse;
            border:1px solid #e5e7eb;
            border-radius:8px;
            overflow:hidden;
        ">
            <thead style="background:#f9fafb;">
                <tr>
                    <th style="padding:12px; text-align:left; font-weight:600;">
                        名前
                    </th>
                    <th style="padding:12px; text-align:left; font-weight:600;">
                        メールアドレス
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $user)
                    <tr>
                        <td style="padding:12px; border-top:1px solid #e5e7eb;">
                            {{ $user->name }}
                        </td>
                        <td style="padding:12px; border-top:1px solid #e5e7eb;">
                            {{ $user->email }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2"
                            style="padding:20px; text-align:center; color:#6b7280;">
                            社員情報が登録されていません
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
