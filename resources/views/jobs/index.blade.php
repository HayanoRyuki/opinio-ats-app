@extends('layouts.menu')

@section('content')
<div style="max-width:960px; margin:0 auto;">

    {{-- ヘッダー --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <h1 style="font-size:1.5rem; font-weight:600; margin-bottom:4px;">
                求人一覧
            </h1>
            <p style="color:#6b7280;">
                登録されている求人を管理できます
            </p>
        </div>

        <a href="{{ route('jobs.create') }}"
           style="background:#111827; color:#fff; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:600;">
            ＋ 新規求人を作成
        </a>
    </div>

    {{-- 求人がない場合 --}}
    @if($jobs->isEmpty())
        <div style="background:#fff; padding:32px; border-radius:12px; text-align:center; color:#6b7280;">
            まだ求人が登録されていません。
        </div>
    @else

    {{-- 求人カード一覧 --}}
    <div style="display:grid; gap:16px;">
        @foreach ($jobs as $job)
            <div style="
                background:#fff;
                padding:20px;
                border-radius:12px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                box-shadow:0 6px 20px rgba(0,0,0,0.05);
            ">

                {{-- 左：求人情報 --}}
                <div>
                    <div style="font-weight:600; font-size:1rem; margin-bottom:4px;">
                        {{ $job->title }}
                    </div>
                    <div style="font-size:0.85rem; color:#6b7280;">
                        {{ $job->location ?? '勤務地未設定' }}
                        ・
                        {{ $job->employment_type ?? '雇用形態未設定' }}
                    </div>
                </div>

                {{-- 右：アクション --}}
                <div style="display:flex; gap:12px;">
                    <a href="{{ route('jobs.pipeline', $job) }}"
                       style="padding:8px 14px; border-radius:6px; border:1px solid #d1d5db; text-decoration:none; color:#111827;">
                        パイプライン
                    </a>

                    <a href="{{ route('jobs.edit', $job) }}"
                       style="padding:8px 14px; border-radius:6px; background:#2563eb; color:#fff; text-decoration:none;">
                        編集
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
