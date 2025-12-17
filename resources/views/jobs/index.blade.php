<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>求人一覧</title>
</head>
<body>

<h1>求人一覧</h1>

<p>
    <a href="{{ route('jobs.create') }}">＋ 新規求人を作成</a>
</p>

<ul>
@foreach ($jobs as $job)
    <li>
        <a href="{{ route('jobs.pipeline', $job) }}">
            {{ $job->title }}
        </a>
    </li>
@endforeach
</ul>

</body>
</html>
