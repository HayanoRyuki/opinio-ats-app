<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>評価入力｜{{ $application->candidate->name }}</title>
</head>

<body class="bg-gray-50">
<div class="p-6" style="max-width:640px; margin:0 auto;">

    <h1 style="font-size:20px; font-weight:bold; margin-bottom:16px;">
        評価入力
    </h1>

    {{-- 応募者情報 --}}
    <div style="
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:12px;
        margin-bottom:16px;
    ">
        <div><strong>候補者：</strong>{{ $application->candidate->name }}</div>
        <div style="font-size:13px; color:#6b7280;">
            {{ $application->candidate->email }}
        </div>
        <div style="margin-top:6px; font-size:13px;">
            <strong>求人：</strong>{{ $application->job->title }}<br>
            <strong>選考段階：</strong>{{ $application->selectionStep->label }}
        </div>
    </div>

    {{-- 評価フォーム --}}
    <form method="POST" action="{{ route('evaluations.store', $application) }}">
        @csrf

        <div style="
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:8px;
            padding:16px;
        ">
            {{-- 総合評価 --}}
            <div style="margin-bottom:12px;">
                <label style="font-weight:600; display:block; margin-bottom:4px;">
                    総合評価（1〜5）
                </label>
                <select name="overall_score" required>
                    <option value="">選択してください</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            {{-- コメント --}}
            <div style="margin-bottom:12px;">
                <label style="font-weight:600; display:block; margin-bottom:4px;">
                    コメント
                </label>
                <textarea
                    name="comment"
                    rows="4"
                    style="width:100%;"
                    placeholder="面接で気になった点、所感など"
                ></textarea>
            </div>

            {{-- 推薦 --}}
            <div style="margin-bottom:12px;">
                <label style="font-weight:600; display:block; margin-bottom:4px;">
                    推薦コメント（任意）
                </label>
                <input
                    type="text"
                    name="recommendation"
                    style="width:100%;"
                    placeholder="次工程に進める / 見送り など"
                >
            </div>

            <button type="submit">
                評価を保存する
            </button>
        </div>
    </form>

</div>
</body>
</html>
