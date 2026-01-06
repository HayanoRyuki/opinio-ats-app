<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Opinio ATS</title>
</head>

@php
    $isLoginPage = request()->routeIs('login');

    // JWT VerifyJwt で積まれた値を使う
    $role = request()->attributes->get('role');

    // 会議用フルメニュー対象
    $isAdminLike = in_array($role, ['admin', 'recruiter'], true);

    $dashboardRoute = match ($role) {
        'admin', 'recruiter' => route('dashboard'),
        'interviewer'        => route('interviewer.dashboard'),
        default              => '#',
    };

    $isDashboardActive =
        ($role === 'interviewer' && request()->routeIs('interviewer.dashboard'))
        || ($isAdminLike && request()->routeIs('dashboard'));

    $active = 'background:#65b891; font-weight:600; border-radius:6px;';
@endphp

<body>

@if (! $isLoginPage)
<nav class="sidebar"
     style="width:240px; font-size:14px; line-height:1.6;
            background:#332c54; color:#f4f4ed; position:fixed; top:0; left:0; height:100vh;">
    <div style="flex:1; overflow-y:auto; padding:16px; height:100%;">

        {{-- ロゴ --}}
        <div style="text-align:center; margin-bottom:32px;">
            <img src="{{ asset('images/atslogo.svg') }}" style="width:160px;">
        </div>

        <ul style="list-style:none; padding:0; margin:0;">

            {{-- ダッシュボード --}}
            <li style="margin-bottom:24px; {{ $isDashboardActive ? $active : '' }}">
                <a href="{{ $dashboardRoute }}"
                   style="display:block; padding:6px 8px; color:#f4f4ed; text-decoration:none;">
                    ダッシュボード
                </a>
            </li>

            {{-- admin / recruiter：会議用フルメニュー --}}
            @if ($isAdminLike)

            {{-- 募集 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">募集</div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px;">評価基準</li>
                    <li>
                        <a href="{{ route('jobs.index') }}"
                           style="display:block; padding:6px 8px; color:#f4f4ed; text-decoration:none;">
                            求人
                        </a>
                    </li>
                </ul>
            </li>

            {{-- 経路 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">経路</div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li>
                        <a href="{{ route('cms.pages.index') }}"
                           style="display:block; padding:6px 8px; color:#f4f4ed; text-decoration:none;">
                            採用ページ
                        </a>
                    </li>
                    <li style="opacity:.5; padding:6px 8px;">媒体</li>
                    <li style="opacity:.5; padding:6px 8px;">エージェント</li>
                    <li style="opacity:.5; padding:6px 8px;">リファラル</li>
                </ul>
            </li>

            {{-- 候補者 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">候補者</div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px;">候補者一覧</li>
                    <li style="opacity:.5; padding:6px 8px;">書類・プロフィール</li>
                    <li style="opacity:.5; padding:6px 8px;">重複チェック</li>
                </ul>
            </li>

            {{-- 選考 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">選考</div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px;">パイプライン</li>
                    <li style="padding:6px 8px;">評価</li>
                    <li style="opacity:.5; padding:6px 8px;">メッセージ</li>
                    <li style="opacity:.5; padding:6px 8px;">採用判断</li>
                </ul>
            </li>

            @endif {{-- /admin / recruiter --}}

            {{-- interviewer 専用 --}}
            @if ($role === 'interviewer')
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">面接</div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="padding:6px 8px;">面接一覧</li>
                    <li style="padding:6px 8px;">面接評価</li>
                </ul>
            </li>
            @endif

            {{-- フッター --}}
            @if ($isAdminLike)
            <li style="margin-top:32px; border-top:1px solid rgba(255,255,255,0.1); padding-top:16px;">
                <a href="{{ route('settings.account') }}"
                   style="display:block; padding:6px 8px; color:#f4f4ed; text-decoration:none;">
                    設定
                </a>
            </li>
            @endif

            <li>
                <a href="{{ route('terms') }}"
                   style="display:block; padding:6px 8px; color:#f4f4ed; text-decoration:none;">
                    利用規約
                </a>
            </li>
            <li>
                <a href="{{ route('privacy') }}"
                   style="display:block; padding:6px 8px; color:#f4f4ed; text-decoration:none;">
                    プライバシー
                </a>
            </li>

        </ul>
    </div>
</nav>
@endif

<main class="main-content"
      style="margin-left:{{ $isLoginPage ? '0' : '240px' }}; min-height:100vh; padding:16px;">
    @yield('content')
</main>

</body>
</html>
