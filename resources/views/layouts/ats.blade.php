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

            {{-- 分析（デフォルト / ダッシュボード） --}}
            <li style="margin-bottom:24px; {{ $active }}">
                <a href="#"
                   style="display:block; padding:8px 10px; color:#f4f4ed; text-decoration:none;">
                    分析（ダッシュボード）
                </a>
            </li>

            {{-- admin / recruiter --}}
            @if ($isAdminLike)

            {{-- 候補者 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    候補者
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="opacity:.5; padding:6px 8px;">候補者一覧</li>
                    <li style="opacity:.5; padding:6px 8px;">パイプライン</li>
                    <li style="opacity:.5; padding:6px 8px;">日程調整</li>
                    <li style="opacity:.5; padding:6px 8px;">メッセージ</li>
                </ul>
            </li>

            {{-- 求人 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    求人
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="opacity:.5; padding:6px 8px;">求人管理</li>
                    <li style="opacity:.5; padding:6px 8px;">採用ページ</li>
                </ul>
            </li>

            {{-- 評価 --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    評価
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="opacity:.5; padding:6px 8px;">評価基準</li>
                    <li style="opacity:.5; padding:6px 8px;">レベル定義</li>
                    <li style="opacity:.5; padding:6px 8px;">質問バンク</li>
                </ul>
            </li>

            {{-- 分析（詳細） --}}
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    分析
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="opacity:.5; padding:6px 8px;">レポート</li>
                    <li style="opacity:.5; padding:6px 8px;">判断履歴</li>
                    <li style="opacity:.5; padding:6px 8px;">面接官別分析</li>
                    <li style="opacity:.5; padding:6px 8px;">チャネル分析</li>
                </ul>
            </li>

            @endif {{-- /admin / recruiter --}}

            {{-- interviewer --}}
            @if ($role === 'interviewer')
            <li style="margin-bottom:24px;">
                <div style="font-size:12px; opacity:.6; margin-bottom:8px;">
                    面接
                </div>
                <ul style="list-style:none; padding-left:12px; margin:0;">
                    <li style="opacity:.5; padding:6px 8px;">面接一覧</li>
                    <li style="opacity:.5; padding:6px 8px;">評価入力</li>
                </ul>
            </li>
            @endif

            {{-- フッター --}}
            @if ($isAdminLike)
            <li style="margin-top:32px; border-top:1px solid rgba(255,255,255,0.1); padding-top:16px;">
                <span style="display:block; padding:6px 8px; opacity:.5;">
                    設定（準備中）
                </span>
            </li>
            @endif

            <li>
                <span style="display:block; padding:6px 8px; opacity:.5;">利用規約</span>
            </li>
            <li>
                <span style="display:block; padding:6px 8px; opacity:.5;">プライバシー</span>
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
