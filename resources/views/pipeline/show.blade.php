<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $job->title }}｜選考パイプライン</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        pre {
            background: #f6f8fa;
            padding: 16px;
            border-radius: 6px;
            overflow-x: auto;
        }
    </style>
</head>
<body>

<h1>{{ $job->title }}｜選考パイプライン</h1>

<p>▼ まずはデータ構造確認用（applications）</p>

<pre>
{{ print_r($job->applications->toArray(), true) }}
</pre>

</body>
</html>
