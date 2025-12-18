{{-- resources/views/layouts/menu.blade.php --}}
<style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    background: #111827;
}

* { box-sizing: border-box; }

/* ===== Sidebar ===== */
.sidebar {
    transition: width 0.25s ease, box-shadow 0.25s ease;
    z-index: 20;
}

.sidebar:hover {
    width: 240px;
    box-shadow: 8px 0 24px rgba(0,0,0,0.25);
}

/* ===== Menu item ===== */
.sidebar-menu-item {
    transition: background-color 0.15s ease;
}

.sidebar-menu-item:hover {
    background:#374151;
    border-radius:6px;
}

/* ===== Main ===== */
.main-content {
    transition: margin-left 0.25s ease, padding 0.25s ease;
}

.main-content.expand {
    margin-left:200px !important;
}
</style>

@php
    $isLoginPage = request()->routeIs('login');
    $activeStyle = 'background:#1f2937; font-weight:600; border-radius:6px;';
@endphp

<div style="font-family:system-ui, -apple-system, BlinkMacSystemFont, sans-serif;">

@if(! $isLoginPage)
<nav class="sidebar" style="
    position:fixed;
    top:0;
    left:0;
    width:220px;
    height:100vh;
    background:#111827;
    color:#fff;
    padding:24px;
    display:flex;
    flex-direction:column;
">

    {{-- ===== 上部（スクロール可） ===== --}}
    <div style="flex:1; overflow-y:auto;">

        {{-- ロゴ --}}
        <div style="text-align:center; margin-bottom:32px;">
            <img src="{{ asset('images/atslogo.svg') }}" style="width:160px;">
        </div>

        <ul style="list-style:none; padding:0; margin:0;">

            {{-- マイページ --}}
            <li class="sidebar-menu-item"
                style="margin-bottom:20px; display:flex; align-items:center;
                {{ request()->routeIs('dashboard') ? $activeStyle : '' }}">
                <img src="{{ asset('images/icons/home.svg') }}" style="width:24px; margin-right:12px;">
                <a href="{{ route('dashboard') }}" style="color:#fff; text-decoration:none;">マイページ</a>
            </li>

            {{-- ===== 求人管理 ===== --}}
            @php
                $jobActive = request()->routeIs('jobs.*');
                $lastJobId = session('last_job_id');
                $lastJobTitle = session('last_job_title');
            @endphp

            <li style="margin-bottom:24px; {{ $jobActive ? $activeStyle : '' }}">

                <div style="display:flex; align-items:center; margin-bottom:8px;">
                    <img src="{{ asset('images/icons/apply.svg') }}" style="width:24px; margin-right:12px;">
                    <span>求人管理</span>
                </div>

                <ul style="list-style:none; padding-left:36px; margin:0;">

                    <li class="sidebar-menu-item"
                        style="margin-bottom:12px; display:flex; align-items:center;
                        {{ request()->routeIs('jobs.index') ? $activeStyle : '' }}">
                        <img src="{{ asset('images/icons/home.svg') }}" style="width:18px; margin-right:8px;">
                        <a href="{{ route('jobs.index') }}" style="color:#fff; text-decoration:none;">求人一覧</a>
                    </li>

                    <li class="sidebar-menu-item"
                        style="margin-bottom:12px; display:flex; align-items:center;
                        {{ request()->routeIs('jobs.create') ? $activeStyle : '' }}">
                        <img src="{{ asset('images/icons/apply.svg') }}" style="width:18px; margin-right:8px;">
                        <a href="{{ route('jobs.create') }}" style="color:#fff; text-decoration:none;">新規求人作成</a>
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
                               style="
                                   color:#fff;
                                   text-decoration:none;
                                   font-size:0.85rem;
                                   font-weight:{{ request()->routeIs('jobs.pipeline') ? '600' : '400' }};
                                   white-space:nowrap;
                                   overflow:hidden;
                                   text-overflow:ellipsis;
                                   max-width:130px;
                               ">
                                {{ $lastJobTitle }}
                            </a>
                        </li>
                    @endif

                </ul>
            </li>

            {{-- ===== 設定系 ===== --}}
            <li style="margin:24px 0; border-top:1px solid rgba(255,255,255,0.1);"></li>

            <li class="sidebar-menu-item"
                style="margin-bottom:16px; display:flex; align-items:center;
                {{ request()->routeIs('settings.account') ? $activeStyle : '' }}">
                <img src="{{ asset('images/icons/home.svg') }}" style="width:22px; margin-right:12px;">
                <a href="{{ route('settings.account') }}" style="color:#fff; text-decoration:none;">アカウント設定</a>
            </li>

            <li class="sidebar-menu-item"
                style="margin-bottom:16px; display:flex; align-items:center;
                {{ request()->routeIs('settings.billing') ? $activeStyle : '' }}">
                <img src="{{ asset('images/icons/home.svg') }}" style="width:22px; margin-right:12px;">
                <a href="{{ route('settings.billing') }}" style="color:#fff; text-decoration:none;">利用プラン・請求</a>
            </li>

            <li class="sidebar-menu-item"
                style="margin-bottom:16px; display:flex; align-items:center;
                {{ request()->routeIs('announcements') ? $activeStyle : '' }}">
                <img src="{{ asset('images/icons/home.svg') }}" style="width:22px; margin-right:12px;">
                <a href="{{ route('announcements') }}" style="color:#fff; text-decoration:none;">お知らせ</a>
            </li>

            <li class="sidebar-menu-item"
                style="margin-bottom:20px; display:flex; align-items:center;
                {{ request()->routeIs('data.policy') ? $activeStyle : '' }}">
                <img src="{{ asset('images/icons/home.svg') }}" style="width:22px; margin-right:12px;">
                <a href="{{ route('data.policy') }}" style="color:#fff; text-decoration:none;">データポリシー</a>
            </li>

            {{-- ログアウト --}}
            <li style="margin:24px 0; border-top:1px solid rgba(255,255,255,0.1);"></li>

            <li class="sidebar-menu-item" style="display:flex; align-items:center;">
                <img src="{{ asset('images/icons/home.svg') }}" style="width:24px; margin-right:12px;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:#fff; cursor:pointer;">
                        ログアウト
                    </button>
                </form>
            </li>

        </ul>
    </div>

    {{-- ===== 下固定 ===== --}}
<ul style="list-style:none; padding:0; margin:0;">

    @php
        $footerActiveStyle = 'background:#1f2937; border-radius:6px; font-weight:600;';
    @endphp

    <li class="sidebar-menu-item"
        style="padding:6px 0; {{ request()->routeIs('ai.policy') ? $footerActiveStyle : '' }}">
        <a href="{{ route('ai.policy') }}"
           style="
               color:#fff;
               text-decoration:none;
               font-size:0.85rem;
               opacity:0.85;
           ">
            AIの使い方
        </a>
    </li>

    <li class="sidebar-menu-item"
        style="padding:6px 0; {{ request()->routeIs('terms') ? $footerActiveStyle : '' }}">
        <a href="{{ route('terms') }}"
           style="
               color:#fff;
               text-decoration:none;
               font-size:0.85rem;
               opacity:0.85;
           ">
            利用規約
        </a>
    </li>

    <li class="sidebar-menu-item"
        style="padding:6px 0; {{ request()->routeIs('company') ? $footerActiveStyle : '' }}">
        <a href="{{ route('company') }}"
           style="
               color:#fff;
               text-decoration:none;
               font-size:0.85rem;
               opacity:0.85;
           ">
            運営会社
        </a>
    </li>

    <li class="sidebar-menu-item"
        style="padding:6px 0; {{ request()->routeIs('privacy') ? $footerActiveStyle : '' }}">
        <a href="{{ route('privacy') }}"
           style="
               color:#fff;
               text-decoration:none;
               font-size:0.85rem;
               opacity:0.85;
           ">
            プライバシーポリシー
        </a>
    </li>

</ul>


</nav>
@endif

{{-- ===== メイン ===== --}}
<main class="main-content"
      style="margin-left:{{ $isLoginPage ? '0' : '220px' }}; min-height:100vh; background:#f9fafb; padding:24px;">
    @yield('content')
</main>

</div>

{{-- ===== ページ遷移アニメーション ===== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const main = document.querySelector('.main-content');
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar || !main) return;

    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', e => {
            if (e.metaKey || e.ctrlKey) return;
            main.classList.add('expand');
            setTimeout(() => window.location = link.href, 120);
            e.preventDefault();
        });
    });
});
</script>
