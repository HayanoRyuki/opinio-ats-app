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

            {{-- ダッシュボード --}}
            <li style="margin-bottom:24px; {{ request()->routeIs('dashboard') ? $active : '' }}">
                <a href="{{ route('dashboard') }}"
                   style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                    ダッシュボード
                </a>
            </li>

            {{-- 募集 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    募集
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="#"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            評価基準
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('jobs.index') }}"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            求人
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 経路 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    経路
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="{{ route('cms.pages.index') }}"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            採用ページ
                        </a>
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        媒体
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        エージェント
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        リファラル
                    </li>
                </ul>
            </li>

            {{-- 候補者 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    候補者
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px; color:#fff;">
                        候補者一覧
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        書類・プロフィール
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        重複チェック
                    </li>
                </ul>
            </li>

            {{-- 選考 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    選考
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px; color:#fff;">
                        パイプライン
                    </li>
                    <li style="padding:6px 8px; color:#fff;">
                        評価
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        メッセージ
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        採用判断
                    </li>
                </ul>
            </li>

            {{-- 面接 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    面接
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px; color:#fff;">
                        日程調整
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        面接評価
                    </li>
                </ul>
            </li>

            {{-- 分析 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    分析
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="{{ route('dashboard') }}"
                           style="display:block; padding:6px 8px; color:#fff; text-decoration:none;">
                            ダッシュボード
                        </a>
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        歩留まり分析
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        チャネル別分析
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        面接官別分析
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">
                        判断履歴
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

        </ul>
    </div>
</nav>
@endif

<main class="main-content" style="margin-left:{{ $isLoginPage ? '0' : '240px' }};">
    @yield('content')
</main>

</body>
</html>
