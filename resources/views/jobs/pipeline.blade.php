{{-- resources/views/jobs/pipeline.blade.php --}}
@extends('layouts.ats')

@section('content')

@php
    $readonly = $readonly ?? false;
@endphp

<div class="pipeline-page">

    {{-- 共有URL表示 or 発行 --}}
    <div class="pipeline-share">
        @if ($job->share_token)
            <input
                type="text"
                readonly
                value="{{ route('jobs.pipeline.share', [$job, $job->share_token]) }}"
                onclick="this.select()"
            >
        @else
            <form method="POST" action="{{ route('jobs.share-token.generate', $job) }}">
                @csrf
                <button type="submit">共有URLを発行</button>
            </form>
        @endif
    </div>

    <h1 class="pipeline-title">
        {{ $job->title }}｜選考パイプライン
    </h1>

    <div class="pipeline-board">

        @foreach ($steps as $step)
            @php
                $applications = $applicationsByStep->get($step->id, collect());
            @endphp

            <div class="pipeline-column" data-step-id="{{ $step->id }}">

                <div class="pipeline-column-header">
                    <span>{{ $step->label }}</span>
                    <span class="pipeline-count">{{ $applications->count() }}</span>
                </div>

                @foreach ($applications as $application)
                    @php
                        $currentOrder = $application->selectionStep->order;
                        $prevStep = $steps->firstWhere('order', $currentOrder - 1);
                        $nextStep = $steps->firstWhere('order', $currentOrder + 1);
                        $decision = $application->hiringDecision;
                    @endphp

                    <div
                        class="application-card {{ $application->isEvaluated() ? 'is-evaluated' : 'is-not-evaluated' }}"
                        data-application-id="{{ $application->id }}"
                    >
                        {{-- ヘッダ --}}
                        <div class="application-header">
                            <span class="application-name">
                                {{ $application->candidate->name }}
                            </span>

                            <span class="badge {{ $application->isEvaluated() ? 'badge-success' : 'badge-warning' }}">
                                {{ $application->isEvaluated() ? '評価済' : '未評価' }}
                            </span>
                        </div>

                        <div class="application-email">
                            {{ $application->candidate->email }}
                        </div>

                        {{-- アクション --}}
                        <div class="application-actions">
                            @if ($job->share_token)
                                <a href="{{ route('applications.share', [$application, $job->share_token]) }}"
                                   target="_blank">
                                    応募者詳細を開く →
                                </a>
                            @endif

                            @if (!$readonly)
                                <a href="{{ route('evaluations.create', $application) }}">
                                    評価する →
                                </a>
                            @endif
                        </div>

                        {{-- 採用判断の表示（色付き＋日時） --}}
                        @if ($decision)
                            @php
                                $decisionMeta = match ($decision->decision) {
                                    'hire' => ['label' => '採用',   'class' => 'bg-green-100 text-green-800'],
                                    'reject' => ['label' => '見送り', 'class' => 'bg-red-100 text-red-800'],
                                    'hold' => ['label' => '保留',   'class' => 'bg-gray-100 text-gray-800'],
                                };
                            @endphp

                            <div class="application-decision-result" style="margin-top:6px;">
                                <strong>採用判断：</strong>
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $decisionMeta['class'] }}">
                                    {{ $decisionMeta['label'] }}
                                </span>

                                @if ($decision->reason)
                                    <div class="text-xs text-gray-500" style="margin-top:4px;">
                                        理由：{{ $decision->reason }}
                                    </div>
                                @endif

                                <div class="text-xs text-gray-400" style="margin-top:2px;">
                                    決定日時：{{ $decision->decided_at->format('Y/m/d H:i') }}
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-gray-400" style="margin-top:6px;">
                                採用判断：未判断
                            </div>
                        @endif

                        {{-- ステップ移動 --}}
                        @if (!$readonly)
                            <div class="application-move">
                                @if ($prevStep)
                                    <form method="POST"
                                          action="{{ route('applications.step.update', $application) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="selection_step_id" value="{{ $prevStep->id }}">
                                        <button type="submit">← 戻す</button>
                                    </form>
                                @endif

                                @if ($nextStep)
                                    <form method="POST"
                                          action="{{ route('applications.step.update', $application) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="selection_step_id" value="{{ $nextStep->id }}">
                                        <button type="submit">次へ →</button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        {{-- 採用判断フォーム（新規 or 上書き） --}}
                        @if (!$readonly)
                            <div class="application-decision-form"
                                 style="margin-top:8px; padding-top:8px; border-top:1px dashed #ccc;">
                                <form
                                    method="POST"
                                    action="{{ route('applications.decision.store', $application->id) }}"
                                    @if ($decision)
                                        onsubmit="return confirm('この採用判断を上書きします。よろしいですか？');"
                                    @endif
                                >
                                    @csrf

                                    <strong>{{ $decision ? '採用判断（上書き）' : '採用判断' }}</strong><br>

                                    <label>
                                        <input type="radio" name="decision" value="hire" required>
                                        採用
                                    </label>

                                    <label>
                                        <input type="radio" name="decision" value="reject">
                                        見送り
                                    </label>

                                    <label>
                                        <input type="radio" name="decision" value="hold">
                                        保留
                                    </label>

                                    <div style="margin-top:6px;">
                                        <textarea
                                            name="reason"
                                            rows="2"
                                            placeholder="判断理由（任意）"
                                        ></textarea>
                                    </div>

                                    <button type="submit" style="margin-top:6px;">
                                        {{ $decision ? '判断を上書き' : '判断を保存' }}
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                @endforeach

            </div>
        @endforeach

    </div>
</div>

{{-- DnD --}}
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

@endsection
