<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Opinio ATS</title>
    @vite(['resources/css/app.css'])
</head>

@php
    $isLoginPage = request()->routeIs('login');
    $active = 'background:#1f2937; font-weight:600; border-radius:6px;';
@endphp

<body>

@if (! $isLoginPage)
<nav class="sidebar"
     style="width:240px; font-size:14px; line-height:1.6;">

    <div style="flex:1; overflow-y:auto; padding:16px;">

        {{-- ロゴ --}}
        <div style="text-align:center; margin-bottom:32px;">
            <img src="{{ asset('images/atslogo.svg') }}" style="width:160px;">
        </div>

        <ul style="list-style:none; padding:0; margin:0;">

            {{-- マイページ --}}
            <li style="margin-bottom:24px; {{ request()->routeIs('dashboard') ? $active : '' }}">
                <a href="{{ route('dashboard') }}"
                   style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                    マイページ
                </a>
            </li>

            {{-- 決める --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    決める
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="#"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            評価基準
                        </a>
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        採用方針
                    </li>
                </ul>
            </li>

            {{-- 集める --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    集める
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="{{ route('jobs.index') }}"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            求人
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cms.pages.index') }}"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            採用ページ
                        </a>
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        エージェント
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        リファラル
                    </li>
                </ul>
            </li>

            {{-- 見極める --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    見極める
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px; color:#fff;">
                        パイプライン
                    </li>
                    <li style="padding:6px 8px; color:#fff;">
                        評価
                    </li>
                    <li style="padding:6px 8px; color:#fff;">
                        日程
                    </li>
                </ul>
            </li>

            {{-- 残す --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    残す
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px; color:#fff;">
                        採用判断
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        判断履歴
                    </li>
                </ul>
            </li>

            {{-- 学ぶ --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    学ぶ
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="{{ route('dashboard') }}"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            ダッシュボード
                        </a>
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        振り返り
                    </li>
                </ul>
            </li>

            {{-- 入社後 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    入社後
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="{{ route('employees.index') }}"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            入社者
                        </a>
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        1on1
                    </li>
                </ul>
            </li>

            {{-- 設定 --}}
            <li style="margin-top:32px; border-top:1px solid rgba(255,255,255,0.1); padding-top:16px;">
                <a href="{{ route('settings.account') }}"
                   style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                    設定
                </a>
            </li>

            <li>
                <a href="{{ route('terms') }}"
                   style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                    利用規約
                </a>
            </li>

            <li>
                <a href="{{ route('privacy') }}"
                   style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                    プライバシー
                </a>
            </li>

            {{--
<li style="margin-top:16px;">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button style="background:none; border:none; color:#fff; cursor:pointer; padding:6px 8px;">
            ログアウト
        </button>
    </form>
</li>
--}}

        </ul>
    </div>
</nav>
@endif

<main class="main-content" style="margin-left:{{ $isLoginPage ? '0' : '240px' }};">
    @yield('content')
</main>

</body>
</html>
