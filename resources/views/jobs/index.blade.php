<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>求人一覧 | Opinio ATS</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        .job-card {
            background: #fff;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .job-title {
            font-size: 18px;
            font-weight: bold;
        }
        .job-meta {
            margin-top: 8px;
            color: #555;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            background: #e5f0ff;
            color: #0047ab;
            font-size: 12px;
            margin-right: 6px;
        }
    </style>
</head>
<body>

<h1>求人一覧</h1>

@foreach ($jobs as $job)
    <div class="job-card">
        <div class="job-title">
            {{ $job->title }}
        </div>

        <div class="job-meta">
            @if($job->category)
                <span class="badge">{{ $job->category->label }}</span>
            @endif

            <span class="badge">status: {{ $job->status }}</span>
        </div>

        <div class="job-meta">
            {{ $job->description }}
        </div>
    </div>
@endforeach

@if($jobs->isEmpty())
    <p>求人がまだありません。</p>
@endif

</body>
</html>
