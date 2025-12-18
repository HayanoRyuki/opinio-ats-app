{{-- resources/views/jobs/pipeline.blade.php --}}
@extends('layouts.menu')

@section('content')

@php
    $readonly = $readonly ?? false;
@endphp

<div class="pipeline-page">

    {{-- 共有URL表示 or 発行 --}}
    @if ($job->share_token)
        <div class="pipeline-share">
            <input
                type="text"
                readonly
                value="{{ route('jobs.pipeline.share', [$job, $job->share_token]) }}"
                onclick="this.select()"
            >
        </div>
    @else
        <form method="POST"
              action="{{ route('jobs.share-token.generate', $job) }}"
              class="pipeline-share">
            @csrf
            <button type="submit">共有URLを発行</button>
        </form>
    @endif

    <h1 class="pipeline-title">
        {{ $job->title }}｜選考パイプライン
    </h1>

    <div class="pipeline-board">

        @foreach ($steps as $step)
            @php
                $applications = $applicationsByStep->get($step->id, collect());
                $count = $applications->count();
            @endphp

            <div class="pipeline-column" data-step-id="{{ $step->id }}">

                <div class="pipeline-column-header">
                    <span>{{ $step->label }}</span>
                    <span class="pipeline-count">{{ $count }}</span>
                </div>

                @foreach ($applications as $application)
                    @php
                        $currentOrder = $application->selectionStep->order;
                        $prevStep = $steps->firstWhere('order', $currentOrder - 1);
                        $nextStep = $steps->firstWhere('order', $currentOrder + 1);
                    @endphp

                    <div
                        class="application-card {{ $application->isEvaluated() ? 'is-evaluated' : 'is-not-evaluated' }}"
                        data-application-id="{{ $application->id }}"
                    >
                        <div class="application-header">
                            <span class="application-name">
                                {{ $application->candidate->name }}
                            </span>

                            @if ($application->isEvaluated())
                                <span class="badge badge-success">評価済</span>
                            @else
                                <span class="badge badge-warning">未評価</span>
                            @endif
                        </div>

                        <div class="application-email">
                            {{ $application->candidate->email }}
                        </div>

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

                        @if (!$readonly)
                            <div class="application-move">
                                @if ($prevStep)
                                    <form method="POST"
                                          action="{{ route('applications.step.update', $application) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden"
                                               name="selection_step_id"
                                               value="{{ $prevStep->id }}">
                                        <button type="submit">← 戻す</button>
                                    </form>
                                @endif

                                @if ($nextStep)
                                    <form method="POST"
                                          action="{{ route('applications.step.update', $application) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden"
                                               name="selection_step_id"
                                               value="{{ $nextStep->id }}">
                                        <button type="submit">次へ →</button>
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
