{{-- resources/views/layouts/menu.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Opinio ATS</title>
    @vite(['resources/css/app.css'])
</head>

@php
    $isLoginPage = request()->routeIs('login');
    $activeStyle = 'background:#1f2937; font-weight:600; border-radius:6px;';

    $jobActive = request()->routeIs('jobs.*') || request()->routeIs('ats.job_roles.*');
    $cmsActive = request()->routeIs('cms.pages.*');

    $lastJobId = session('last_job_id');
    $lastJobTitle = session('last_job_title');
@endphp

<body>

@if(! $isLoginPage)
<nav class="sidebar">

    {{-- ===== 上部（スクロール可） ===== --}}
    <div style="flex:1; overflow-y:auto;">

        {{-- ロゴ --}}
        <div style="text-align:center; margin-bottom:32px;">
            <img src="{{ asset('images/atslogo.svg') }}" style="width:160px;">
        </div>

        <ul style="list-style:none; padding:0; margin:0;">

            {{-- ====================
                マイページ
            ==================== --}}
            <li class="sidebar-menu-item"
                style="margin-bottom:20px; display:flex; align-items:center;
                {{ request()->routeIs('dashboard') ? $activeStyle : '' }}">
                <img src="{{ asset('images/icons/home.svg') }}" style="width:24px; margin-right:12px;">
                <a href="{{ route('dashboard') }}" style="color:#fff; text-decoration:none;">
                    マイページ
                </a>
            </li>

            {{-- ====================
                求人管理
            ==================== --}}
            <li style="margin-bottom:24px; {{ $jobActive ? $activeStyle : '' }}">

                <div style="display:flex; align-items:center; margin-bottom:8px;">
                    <img src="{{ asset('images/icons/apply.svg') }}" style="width:24px; margin-right:12px;">
                    <span>求人管理</span>
                </div>

                <ul style="list-style:none; padding-left:36px; margin:0;">

                    {{-- 求人一覧 --}}
                    <li class="sidebar-menu-item"
                        style="margin-bottom:12px; display:flex; align-items:center;
                        {{ request()->routeIs('jobs.index') ? $activeStyle : '' }}">
                        <img src="{{ asset('images/icons/list.svg') }}" style="width:18px; margin-right:8px;">
                        <a href="{{ route('jobs.index') }}" style="color:#fff; text-decoration:none;">
                            求人一覧
                        </a>
                    </li>

                    {{-- 新規求人作成 --}}
                    <li class="sidebar-menu-item"
                        style="margin-bottom:12px; display:flex; align-items:center;
                        {{ request()->routeIs('jobs.create') ? $activeStyle : '' }}">
                        <img src="{{ asset('images/icons/edit.svg') }}" style="width:18px; margin-right:8px;">
                        <a href="{{ route('jobs.create') }}" style="color:#fff; text-decoration:none;">
                            新規求人作成
                        </a>
                    </li>

                    {{-- 職種管理 --}}
                    <li class="sidebar-menu-item"
                        style="margin-bottom:12px; display:flex; align-items:center;
                        {{ request()->routeIs('ats.job_roles.*') ? $activeStyle : '' }}">
                        <img src="{{ asset('images/icons/tag.svg') }}" style="width:18px; margin-right:8px;">
                        <a href="{{ route('ats.job_roles.index') }}" style="color:#fff; text-decoration:none;">
                            職種管理
                        </a>
                    </li>

                    {{-- 最近の求人 --}}
                    @if($lastJobId)
                        <li style="margin:16px 0 6px; font-size:0.75rem; opacity:0.6;">
                            最近の求人
                        </li>

                        <li class="sidebar-menu-item"
                            style="
                                margin-bottom:12px;
                                display:flex;
                                align-items:center;
                                padding:6px 8px;
                                border-radius:6px;
                                background:{{ request()->routeIs('jobs.pipeline') ? 'rgba(255,255,255,0.15)' : 'rgba(255,255,255,0.08)' }};
                            ">
                            <img src="{{ asset('images/icons/apply.svg') }}" style="width:16px; margin-right:8px;">
                            <a href="{{ route('jobs.pipeline', $lastJobId) }}"
                               style="color:#fff; text-decoration:none; font-size:0.85rem;">
                                {{ $lastJobTitle }}
                            </a>
                        </li>
                    @endif

                </ul>
            </li>

            {{-- ====================
                CMS：ページ管理
            ==================== --}}
            <li style="margin-bottom:24px; {{ $cmsActive ? $activeStyle : '' }}">

                <div style="display:flex; align-items:center; margin-bottom:8px;">
                    <img src="{{ asset('images/icons/pages.svg') }}" style="width:24px; margin-right:12px;">
                    <span>ページ管理</span>
                </div>

                <ul style="list-style:none; padding-left:36px; margin:0;">

                    {{-- 求人ページ一覧 --}}
                    <li class="sidebar-menu-item"
                        style="margin-bottom:12px; display:flex; align-items:center;
                        {{ request()->routeIs('cms.pages.index') ? $activeStyle : '' }}">
                        <img src="{{ asset('images/icons/list.svg') }}" style="width:18px; margin-right:8px;">
                        <a href="{{ route('cms.pages.index') }}" style="color:#fff; text-decoration:none;">
                            求人ページ一覧
                        </a>
                    </li>

                    {{-- 求人ページ作成 --}}
                    <li class="sidebar-menu-item"
                        style="margin-bottom:12px; display:flex; align-items:center;
                        {{ request()->routeIs('cms.pages.create') ? $activeStyle : '' }}">
                        <img src="{{ asset('images/icons/edit.svg') }}" style="width:18px; margin-right:8px;">
                        <a href="{{ route('cms.pages.create') }}" style="color:#fff; text-decoration:none;">
                            求人ページ作成
                        </a>
                    </li>

                </ul>
            </li>

            {{-- ====================
                設定系
            ==================== --}}
            <li style="margin:24px 0; border-top:1px solid rgba(255,255,255,0.1);"></li>

            <li class="sidebar-menu-item">
                <a href="{{ route('settings.account') }}" style="color:#fff;">アカウント設定</a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('settings.billing') }}" style="color:#fff;">利用プラン・請求</a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('announcements') }}" style="color:#fff;">お知らせ</a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('data.policy') }}" style="color:#fff;">データポリシー</a>
            </li>

            {{-- ====================
                フッター
            ==================== --}}
            <li style="margin:24px 0; border-top:1px solid rgba(255,255,255,0.1);"></li>

            <li class="sidebar-menu-item"><a href="{{ route('ai.policy') }}" style="color:#fff;">AIの使い方</a></li>
            <li class="sidebar-menu-item"><a href="{{ route('terms') }}" style="color:#fff;">利用規約</a></li>
            <li class="sidebar-menu-item"><a href="{{ route('company') }}" style="color:#fff;">運営会社</a></li>
            <li class="sidebar-menu-item"><a href="{{ route('privacy') }}" style="color:#fff;">プライバシーポリシー</a></li>

            {{-- ログアウト --}}
            <li class="sidebar-menu-item" style="margin-top:16px;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button style="background:none; border:none; color:#fff; cursor:pointer;">
                        ログアウト
                    </button>
                </form>
            </li>

        </ul>
    </div>
</nav>
@endif

<main class="main-content" style="margin-left:{{ $isLoginPage ? '0' : '220px' }};">
    @yield('content')
</main>

</body>
</html>
