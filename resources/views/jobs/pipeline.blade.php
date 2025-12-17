<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $job->title }}｜選考パイプライン</title>
</head>
<body class="bg-gray-50">
    <div class="p-6">
        <h1>{{ $job->title }}｜選考パイプライン</h1>

<div style="display:flex; gap:24px; align-items:flex-start;">
    @foreach ($steps as $step)
        <div style="min-width:220px; background:#f7f7f7; padding:12px; border-radius:6px;">
            <h3>{{ $step->label }}</h3>

            @foreach ($applicationsByStep[$step->id] ?? [] as $application)
                <div style="background:#fff; border:1px solid #ddd; padding:8px; margin-bottom:8px;">
                    <strong>{{ $application->candidate->name }}</strong><br>
                    <small>{{ $application->candidate->email }}</small>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

    </div>
</body>
</html>
