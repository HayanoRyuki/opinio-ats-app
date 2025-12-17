<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $job->title }}｜選考パイプライン</title>
</head>

@php
    $readonly = $readonly ?? false;
@endphp

<body class="bg-gray-50">
<div class="p-6">

    {{-- 共有URL表示 or 発行 --}}
    @if ($job->share_token)
        <div style="margin-bottom:16px;">
            <input
                type="text"
                readonly
                value="{{ route('jobs.pipeline.share', [$job, $job->share_token]) }}"
                style="width:100%; font-size:12px; padding:6px;"
                onclick="this.select()"
            >
        </div>
    @else
        <form method="POST" action="{{ route('jobs.share-token.generate', $job) }}" style="margin-bottom:16px;">
            @csrf
            <button>共有URLを発行</button>
        </form>
    @endif

    <h1 style="font-size:20px; font-weight:bold; margin-bottom:24px;">
        {{ $job->title }}｜選考パイプライン
    </h1>

    <div style="display:flex; gap:24px; align-items:flex-start;">

        @foreach ($steps as $step)
            @php
                $applications = $applicationsByStep[$step->id] ?? [];
                $count = count($applications);
            @endphp

            {{-- パイプラインカラム --}}
            <div
                class="pipeline-column"
                data-step-id="{{ $step->id }}"
                style="
                    min-width:220px;
                    background:#f7f7f7;
                    padding:12px;
                    border-radius:6px;
                "
            >
                {{-- 見出し + 件数 --}}
                <h3 style="
                    margin-bottom:12px;
                    font-weight:bold;
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                ">
                    <span>{{ $step->label }}</span>
                    <span style="
                        background:#555;
                        color:#fff;
                        font-size:12px;
                        padding:2px 8px;
                        border-radius:12px;
                    ">
                        {{ $count }}
                    </span>
                </h3>

                {{-- 応募者カード --}}
                @foreach ($applications as $application)
                    @php
                        $currentOrder = $application->selectionStep->order;
                        $prevStep = $steps->firstWhere('order', $currentOrder - 1);
                        $nextStep = $steps->firstWhere('order', $currentOrder + 1);
                    @endphp

                    <div
                        class="application-card"
                        data-application-id="{{ $application->id }}"
                        style="
                            background:#fff;
                            border:1px solid #ddd;
                            padding:8px;
                            margin-bottom:8px;
                            border-radius:4px;
                            {{ $readonly ? 'cursor:default;' : 'cursor:move;' }}
                        "
                    >
                        <strong>{{ $application->candidate->name }}</strong><br>
                        <small>{{ $application->candidate->email }}</small>

                        {{-- 応募者単体・共有ビュー --}}
                        @if ($job->share_token)
                            <div style="margin-top:6px;">
                                <a
                                    href="{{ route('applications.share', [$application, $job->share_token]) }}"
                                    target="_blank"
                                    style="font-size:11px; text-decoration:underline;"
                                >
                                    共有ビューを開く
                                </a>
                            </div>
                        @endif

                        {{-- ステップ移動ボタン（readonly時は非表示） --}}
                        @if (!$readonly)
                            <div style="display:flex; gap:4px; margin-top:6px;">
                                @if ($prevStep)
                                    <form method="POST" action="{{ route('applications.step.update', $application) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="selection_step_id" value="{{ $prevStep->id }}">
                                        <button style="font-size:11px;">← 戻す</button>
                                    </form>
                                @endif

                                @if ($nextStep)
                                    <form method="POST" action="{{ route('applications.step.update', $application) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="selection_step_id" value="{{ $nextStep->id }}">
                                        <button style="font-size:11px;">次へ →</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach

    </div>
</div>

{{-- ドラッグ＆ドロップ（readonly時は無効） --}}
@if (!$readonly)
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.querySelectorAll('.pipeline-column').forEach(column => {
    new Sortable(column, {
        group: 'pipeline',
        animation: 150,
        onEnd: function (evt) {
            const applicationId = evt.item.dataset.applicationId;
            const newStepId = evt.to.dataset.stepId;
            if (!applicationId || !newStepId) return;

            fetch(`/applications/${applicationId}/step`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    selection_step_id: newStepId
                })
            });
        }
    });
});
</script>
@endif

</body>
</html>
