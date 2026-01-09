<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Opinio ATS</title>

    <script src="https://cdn.tailwindcss.com"></script>

    {{-- 既存CSS --}}
    <link rel="stylesheet" href="{{ asset('css/ats.css') }}">
</head>

@php
    $isLoginPage = request()->routeIs('login');

    // VerifyJwt middleware で積まれた role
    $role = request()->attributes->get('role');

    // 管理・採用担当
    $isAdminLike = in_array($role, ['admin', 'recruiter'], true);

    // 大メニュー判定（route name の prefix）
    $routeName  = request()->route()?->getName() ?? '';
    $activeMain = explode('.', $routeName)[0] ?? 'candidates';

    // 中メニュー用スタイル
    $linkDone = 'display:block; padding:6px 8px; color:#f4f4ed; text-decoration:none; border-radius:6px;';
    $linkTodo = 'display:block; padding:6px 8px; color:rgba(244,244,237,0.35); cursor:not-allowed;';
@endphp

<body>

@if (! $isLoginPage)

{{-- =========================
   上バー（大メニュー）
========================= --}}
<nav style="
    height:56px;
    background:#332c54;
    color:#f4f4ed;
    display:flex;
    align-items:center;
">

    {{-- 左：ロゴ（左サイド中央揃え・大きめ） --}}
    <div style="
        width:240px;
        display:flex;
        align-items:center;
        justify-content:center;
    ">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/atslogo.svg') }}"
                 style="height:40px;">
        </a>
    </div>

    {{-- 中央：大メニュー --}}
    <ul style="
        display:flex;
        gap:32px;
        list-style:none;
        margin:0;
        padding-left:24px;
    ">
        @foreach ([
            'candidates'  => ['👥','候補者'],
            'jobs'        => ['💼','求人'],
            'evaluations' => ['⭐','評価'],
            'analytics'   => ['📊','分析'],
        ] as $key => [$icon, $label])
        <li>
            <a href="/{{ $key }}"
               style="
                 display:flex;
                 align-items:center;
                 gap:8px;
                 padding:8px 14px;
                 font-size:16px;
                 font-weight:600;
                 border-radius:8px;
                 text-decoration:none;
                 color:#f4f4ed;
                 {{ $activeMain === $key ? 'background:#65b891;' : '' }}
               ">
                <span>{{ $icon }}</span>
                <span>{{ $label }}</span>
            </a>
        </li>
        @endforeach
    </ul>

    {{-- 右：ユーティリティ --}}
    <div style="
        margin-left:auto;
        padding-right:24px;
        display:flex;
        gap:16px;
    ">
        <a href="/notifications">🔔</a>
        <a href="/me">👤</a>
        @if ($isAdminLike)
            <a href="/settings">⚙️</a>
        @endif
    </div>

</nav>
@endif

{{-- =========================
   メインレイアウト
========================= --}}
<div style="display:flex;">

@if (! $isLoginPage)

{{-- =========================
   中メニュー（左）
========================= --}}
<nav style="
    width:240px;
    background:#332c54;
    color:#f4f4ed;
    font-size:14px;
    line-height:1.6;
    height:calc(100vh - 56px);
">

<div style="padding:16px; height:100%; overflow-y:auto;">

{{-- 👥 候補者 --}}
@if ($activeMain === 'candidates')
    <div style="font-size:12px; opacity:.5; margin-bottom:12px;">候補者</div>
    <ul style="list-style:none; padding:0; margin:0;">
        <li><a href="/candidates" style="{{ $linkDone }}">候補者一覧</a></li>
        <li><a href="/applications" style="{{ $linkDone }}">応募</a></li>
        <li><a href="/pipeline" style="{{ $linkDone }}">パイプライン</a></li>
        <li><a href="/interviews" style="{{ $linkDone }}">面接</a></li>
    </ul>
@endif

{{-- 💼 求人（仮） --}}
@if ($activeMain === 'jobs')
    <div style="font-size:12px; opacity:.5; margin-bottom:12px;">求人</div>
    <ul style="list-style:none; padding:0; margin:0;">
        <li><a href="/jobs" style="{{ $linkDone }}">求人管理</a></li>
        <li><div style="{{ $linkTodo }}">求人詳細</div></li>
        <li><div style="{{ $linkTodo }}">採用ページ</div></li>
    </ul>
@endif

{{-- ⭐ 評価 --}}
@if ($activeMain === 'evaluations')
    <div style="font-size:12px; opacity:.5; margin-bottom:12px;">評価</div>
    <ul style="list-style:none; padding:0; margin:0;">
        <li><a href="/interviews" style="{{ $linkDone }}">面接評価</a></li>
        <li><div style="{{ $linkTodo }}">評価基準</div></li>
        <li><div style="{{ $linkTodo }}">判断履歴</div></li>
    </ul>
@endif

{{-- 📊 分析 --}}
@if ($activeMain === 'analytics')
    <div style="font-size:12px; opacity:.5; margin-bottom:12px;">分析</div>
    <ul style="list-style:none; padding:0; margin:0;">
        <li><a href="{{ route('dashboard') }}" style="{{ $linkDone }}">ダッシュボード</a></li>
        <li><a href="/reports" style="{{ $linkDone }}">レポート</a></li>
        <li><div style="{{ $linkTodo }}">KPI</div></li>
    </ul>
@endif

</div>
</nav>
@endif

{{-- =========================
   メインコンテンツ
========================= --}}
<main style="flex:1; padding:16px;">
    @yield('content')
</main>

</div>

</body>
</html>
