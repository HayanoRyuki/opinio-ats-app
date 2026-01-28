<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $page->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css'])
</head>
<body style="background:#f9fafb;">

<main style="max-width:800px; margin:40px auto; background:#fff; padding:40px; border-radius:12px;">

    {{-- タイトル --}}
    <h1 style="font-size:1.8rem; font-weight:700; margin-bottom:8px;">
        {{ $page->title }}
    </h1>

    {{-- 紐づく求人 --}}
    @if($page->job)
        <div style="color:#6b7280; margin-bottom:24px;">
            募集職種：{{ $page->job->title }}
        </div>
    @endif

    {{-- 本文 --}}
    <div style="line-height:1.8; font-size:1rem;">
        {!! nl2br(e($page->content)) !!}
    </div>

</main>

</body>
</html>
