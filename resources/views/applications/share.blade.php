<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>
        {{ $application->candidate->name }}｜
        {{ $job->title }}（応募者詳細）
    </title>
</head>

@php
    $readonly = $readonly ?? true;
@endphp

<body class="bg-gray-50">
<div class="p-6">

    <h1 style="font-size:20px; font-weight:bold; margin-bottom:16px;">
        応募者詳細（共有）
    </h1>

    {{-- 求人情報 --}}
    <div style="margin-bottom:16px;">
        <strong>求人：</strong>{{ $job->title }}<br>
        <strong>ステータス：</strong>{{ $application->selectionStep->label }}
    </div>

    {{-- 候補者情報 --}}
    <div style="
        background:#fff;
        border:1px solid #ddd;
        padding:12px;
        border-radius:6px;
        margin-bottom:16px;
    ">
        <h2 style="font-size:16px; margin-bottom:8px;">候補者</h2>
        <div><strong>氏名：</strong>{{ $application->candidate->name }}</div>
        <div><strong>Email：</strong>{{ $application->candidate->email }}</div>
    </div>

    {{-- OpinioMeet（C-3） --}}
    <div style="
        background:#fff;
        border:1px solid #ddd;
        padding:12px;
        border-radius:6px;
        margin-bottom:16px;
    ">
        <h2 style="font-size:16px; margin-bottom:8px;">
            事前ヒアリング（OpinioMeet）
        </h2>

        @if ($application->opinio_meet_url)
            <a
                href="{{ $application->opinio_meet_url }}"
                target="_blank"
                style="font-size:13px; text-decoration:underline;"
            >
                OpinioMeet の結果を見る
            </a>
        @else
            <p style="color:#666; font-size:13px;">
                まだ OpinioMeet は実施されていません。
            </p>
        @endif
    </div>

    {{-- 評価（将来拡張） --}}
    <div style="
        background:#fff;
        border:1px solid #ddd;
        padding:12px;
        border-radius:6px;
    ">
        <h2 style="font-size:16px; margin-bottom:8px;">評価</h2>
        <p style="color:#666; font-size:13px;">
            ※ evaluations をここに統合予定
        </p>
    </div>

</div>
</body>
</html>
