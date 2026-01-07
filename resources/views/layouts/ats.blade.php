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

    // 状態別スタイル
    $active   = 'background:#65b891; font-weight:600; border-radius:6px;';
    $hoverBg  = '#3f3768';

    // 進捗可視化用
    $linkDone = 'display:block; padding:6px 8px; color:#f4f4ed; text-decoration:underline; border-radius:6px; cursor:pointer;';
    $linkTodo = 'display:block; padding:6px 8px; color:rgba(244,244,237,0.35); text-decoration:none; cursor:not-allowed;';
@endphp

<body>

@if (! $isLoginPage)
<nav class="sidebar"
     style="width:240px; font-size:14px; line-height:1.6;
            background:#332c54; color:#f4f4ed;
            position:fixed; top:0; left:0; height:100vh;">

    <div style="padding:16px; height:100%; overflow-y:auto;">

        {{-- ロゴ（要件：ダッシュボード遷移） --}}
        <div style="text-align:center; margin-bottom:32px;">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/atslogo.svg') }}" style="width:160px;">
            </a>
        </div>

        <ul style="list-style:none; padding:0; margin:0;">

            {{-- 分析（ダッシュボード：要件デフォルト） --}}
            <li style="margin-bottom:24px;">
                <a href="{{ route('dashboard') }}"
                   style="display:block; padding:8px 10px; color:#f4f4ed;
                          text-decoration:underline; border-radius:6px;
                          {{ request()->routeIs('dashboard') ? $active : '' }}"
                   onmouseover="this.style.background='{{ $hoverBg }}'"
                   onmouseout="this.style.background='{{ request()->routeIs('dashboard') ? '#65b891' : 'transparent' }}'">
                    分析（ダッシュボード）
                </a>
            </li>

            {{-- 管理者 / 採用担当 --}}
            @if ($isAdminLike)

            {{-- 候補者（実装済） --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px;">
                    候補者
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/candidates" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            候補者一覧
                        </a>
                    </li>
                    <li>
                        <a href="/applications" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            応募
                        </a>
                    </li>
                    <li>
                        <a href="/pipeline" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            パイプライン
                        </a>
                    </li>
                    <li>
                        <a href="/interviews" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            面接
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 求人（部分実装） --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px;">
                    求人
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/jobs" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            求人管理
                        </a>
                    </li>
                    <li><div style="{{ $linkTodo }}">求人詳細</div></li>
                    <li><div style="{{ $linkTodo }}">採用ページ</div></li>
                </ul>
            </li>

            {{-- 評価（部分実装） --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px;">
                    評価
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/interviews" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            面接評価
                        </a>
                    </li>
                    <li><div style="{{ $linkTodo }}">評価基準管理</div></li>
                </ul>
            </li>

            {{-- 分析（部分実装） --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px;">
                    分析
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="{{ route('dashboard') }}" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            ダッシュボード
                        </a>
                    </li>
                    <li>
                        <a href="/reports" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            レポート
                        </a>
                    </li>
                    <li><div style="{{ $linkTodo }}">判断履歴</div></li>
                </ul>
            </li>

            {{-- システム（未実装・サイズ感提示） --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px;">
                    システム
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li><div style="{{ $linkTodo }}">🔔 通知</div></li>
                    <li><div style="{{ $linkTodo }}">👤 ユーザー</div></li>
                    <li><div style="{{ $linkTodo }}">⚙️ 設定</div></li>
                </ul>
            </li>

            @endif {{-- /admin / recruiter --}}

            {{-- 面接官 --}}
            @if ($role === 'interviewer')
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px;">
                    面接
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/interviewer/dashboard" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            ダッシュボード
                        </a>
                    </li>
                    <li>
                        <a href="/interviews" style="{{ $linkDone }}"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            面接一覧
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- フッター --}}
            <li style="margin-top:32px; border-top:1px solid rgba(255,255,255,0.1); padding-top:16px;">
                <a href="/terms" style="{{ $linkDone }}"
                   onmouseover="this.style.background='{{ $hoverBg }}'"
                   onmouseout="this.style.background='transparent'">
                    利用規約
                </a>
                <a href="/privacy" style="{{ $linkDone }}"
                   onmouseover="this.style.background='{{ $hoverBg }}'"
                   onmouseout="this.style.background='transparent'">
                    プライバシー
                </a>
            </li>

        </ul>
    </div>
</nav>
@endif

<main class="main-content"
      style="margin-left:{{ $isLoginPage ? '0' : '240px' }};
             min-height:100vh; padding:16px;">
    @yield('content')
</main>

</body>
</html>
