@extends('layouts.ats')

@section('content')

<h1>{{ $job->title }}｜選考パイプライン（デバッグ用）</h1>

<p>▼ まずはデータ構造確認用（applications）</p>

<pre style="background:#f6f8fa; padding:16px; border-radius:6px; overflow-x:auto;">
{{ print_r($job->applications->toArray(), true) }}
</pre>

@endsection
