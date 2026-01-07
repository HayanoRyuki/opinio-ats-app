<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Opinio ATS</title>
</head>

@php
    $isLoginPage = request()->routeIs('login');

    // VerifyJwt middleware で積まれた role
    $role = request()->attributes->get('role');

    // 管理・採用担当
    $isAdminLike = in_array($role, ['admin', 'recruiter'], true);

    $active = 'background:#65b891; font-weight:600; border-radius:6px;';
    $hoverBg = '#3f3768';
@endphp

<body>

@if (! $isLoginPage)
<nav class="sidebar"
     style="width:240px; font-size:14px; line-height:1.6;
            background:#332c54; color:#f4f4ed;
            position:fixed; top:0; left:0; height:100vh;">

    <div style="padding:16px; height:100%; overflow-y:auto;">

        {{-- ロゴ --}}
        <div style="text-align:center; margin-bottom:32px;">
            <img src="{{ asset('images/atslogo.svg') }}" style="width:160px;">
        </div>

        <ul style="list-style:none; padding:0; margin:0;">

            {{-- ダッシュボード --}}
            <li style="margin-bottom:24px;">
                <a href="{{ route('dashboard') }}"
                   style="display:block; padding:8px 10px; color:#f4f4ed;
                          text-decoration:none; border-radius:6px; cursor:pointer;
                          {{ request()->routeIs('dashboard') ? $active : '' }}"
                   onmouseover="this.style.background='{{ $hoverBg }}'"
                   onmouseout="this.style.background='{{ request()->routeIs('dashboard') ? '#65b891' : 'transparent' }}'">
                    分析（ダッシュボード）
                </a>
            </li>

            {{-- 管理者 / 採用担当 --}}
            @if ($isAdminLike)

            {{-- 候補者 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px; letter-spacing:0.05em;">
                    候補者
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/candidates"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            候補者一覧
                        </a>
                    </li>
                    <li>
                        <a href="/applications"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            応募
                        </a>
                    </li>
                    <li>
                        <a href="/pipeline"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            パイプライン
                        </a>
                    </li>
                    <li>
                        <a href="/interviews"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            面接
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 求人 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px; letter-spacing:0.05em;">
                    求人
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/jobs"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            求人管理
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 評価 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px; letter-spacing:0.05em;">
                    評価
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/interviews"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            面接評価
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 分析 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px; letter-spacing:0.05em;">
                    分析
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/reports"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            レポート
                        </a>
                    </li>
                </ul>
            </li>

            @endif {{-- /admin / recruiter --}}

            {{-- 面接官 --}}
            @if ($role === 'interviewer')
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.5; margin-bottom:8px; letter-spacing:0.05em;">
                    面接
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="/interviewer/dashboard"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
                           onmouseover="this.style.background='{{ $hoverBg }}'"
                           onmouseout="this.style.background='transparent'">
                            ダッシュボード
                        </a>
                    </li>
                    <li>
                        <a href="/interviews"
                           style="display:block; padding:6px 8px; color:#f4f4ed;
                                  border-radius:6px; cursor:pointer;"
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
                <a href="/terms"
                   style="display:block; padding:6px 8px; opacity:.5; color:#f4f4ed;
                          border-radius:6px; cursor:pointer;"
                   onmouseover="this.style.background='{{ $hoverBg }}'"
                   onmouseout="this.style.background='transparent'">
                    利用規約
                </a>
                <a href="/privacy"
                   style="display:block; padding:6px 8px; opacity:.5; color:#f4f4ed;
                          border-radius:6px; cursor:pointer;"
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
