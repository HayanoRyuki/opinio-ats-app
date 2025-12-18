@extends('layouts.menu')

@section('content')

@php
    $readonly = $readonly ?? false;
@endphp

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
                    border-radius:8px;
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
                        background:#111827;
                        color:#fff;
                        font-size:11px;
                        padding:2px 8px;
                        border-radius:999px;
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
                            background: {{ $application->isEvaluated() ? '#ffffff' : '#fff7ed' }};
                            border:1px solid {{ $application->isEvaluated() ? '#e5e7eb' : '#fed7aa' }};
                            padding:10px;
                            margin-bottom:10px;
                            border-radius:8px;
                            box-shadow:0 1px 2px rgba(0,0,0,0.05);
                            {{ $readonly ? 'cursor:default;' : 'cursor:grab;' }}
                        "
                    >
                        {{-- 名前 + 評価ステータス --}}
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div style="font-weight:600; font-size:14px;">
                                {{ $application->candidate->name }}
                            </div>

                            @if ($application->isEvaluated())
                                <span style="
                                    font-size:10px;
                                    padding:2px 6px;
                                    border-radius:999px;
                                    background:#dcfce7;
                                    color:#166534;
                                ">
                                    評価済
                                </span>
                            @else
                                <span style="
                                    font-size:10px;
                                    padding:2px 6px;
                                    border-radius:999px;
                                    background:#ffedd5;
                                    color:#9a3412;
                                ">
                                    未評価
                                </span>
                            @endif
                        </div>

                        {{-- メール（薄く） --}}
                        <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                            {{ $application->candidate->email }}
                        </div>

                        {{-- CTA群 --}}
                        <div style="margin-top:8px; display:flex; flex-direction:column; gap:4px;">

                            {{-- 応募者詳細（共有ビュー） --}}
                            @if ($job->share_token)
                                <a
                                    href="{{ route('applications.share', [$application, $job->share_token]) }}"
                                    target="_blank"
                                    style="
                                        font-size:12px;
                                        color:#2563eb;
                                        text-decoration:none;
                                    "
                                >
                                    応募者詳細を開く →
                                </a>
                            @endif

                            {{-- 評価する --}}
                            @if (!$readonly)
                                <a
                                    href="{{ route('evaluations.create', $application) }}"
                                    style="
                                        font-size:12px;
                                        color:#16a34a;
                                        text-decoration:none;
                                    "
                                >
                                    評価する →
                                </a>
                            @endif
                        </div>

                        {{-- ステップ移動ボタン --}}
                        @if (!$readonly)
                            <div style="display:flex; gap:6px; margin-top:8px;">
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

{{-- ドラッグ＆ドロップ --}}
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
