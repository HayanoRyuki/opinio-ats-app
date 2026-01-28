<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>
        {{ $application->candidate->name }}｜
        {{ $job->title }}（応募者詳細）
    </title>
</head>

<body class="bg-gray-50">
<div class="p-6" style="max-width:720px; margin:0 auto;">

    <h1 style="font-size:20px; font-weight:bold; margin-bottom:16px;">
        応募者詳細（共有）
    </h1>

    {{-- 共通カード：求人情報 --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:12px;
        margin-bottom:16px;
    ">
        <div style="font-size:13px; color:#6b7280;">求人</div>
        <div style="font-weight:600;">{{ $job->title }}</div>
        <div style="margin-top:6px; font-size:13px;">
            <strong>選考段階：</strong>{{ $application->selectionStep->label }}
        </div>
    </div>

    {{-- 共通カード：候補者 --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:12px;
        margin-bottom:16px;
    ">
        <div style="font-size:13px; color:#6b7280;">候補者</div>
        <div style="font-weight:600;">{{ $application->candidate->name }}</div>
        <div style="font-size:13px; color:#6b7280;">
            {{ $application->candidate->email }}
        </div>
    </div>

    {{-- 共通カード：OpinioMeet --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:12px;
        margin-bottom:16px;
    ">
        <div style="font-size:13px; color:#6b7280; margin-bottom:4px;">
            事前ヒアリング（OpinioMeet）
        </div>

        @if ($application->opinio_meet_url)
            <a
                href="{{ $application->opinio_meet_url }}"
                target="_blank"
                style="font-size:13px; color:#2563eb; text-decoration:none;"
            >
                結果を開く →
            </a>
        @else
            <div style="font-size:13px; color:#6b7280;">
                未実施
            </div>
        @endif
    </div>

    {{-- 評価履歴タイムライン --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:12px;
    ">
        <div style="font-size:13px; color:#6b7280; margin-bottom:6px;">
            評価履歴
        </div>

        @if ($application->evaluations->isEmpty())
            <div style="font-size:13px; color:#6b7280;">
                まだ評価は登録されていません。
            </div>
        @else
            <ul style="list-style:none; padding:0; margin:0;">
                @foreach ($application->evaluations as $evaluation)
                    <li style="
                        margin-bottom:8px;
                        padding-bottom:6px;
                        border-bottom:1px solid #e5e7eb;
                    ">
                        <div style="font-size:12px; font-weight:600;">
                            {{ $evaluation->user->name ?? '評価者不明' }} 
                            <span style="font-weight:400; color:#6b7280;">
                                ({{ $evaluation->created_at->format('Y/m/d H:i') }})
                            </span>
                        </div>
                        <div style="font-size:12px; margin-top:2px;">
                            総合評価：★ {{ $evaluation->overall_score }}
                        </div>
                        @if ($evaluation->comment)
                            <div style="font-size:12px; margin-top:2px;">
                                コメント：{{ $evaluation->comment }}
                            </div>
                        @endif
                        @if ($evaluation->recommendation)
                            <div style="font-size:12px; margin-top:2px;">
                                所見：{{ $evaluation->recommendation }}
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
</body>
</html>
