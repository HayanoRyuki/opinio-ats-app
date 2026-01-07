@extends('layouts.ats')

@section('content')
    <h1>応募者詳細（閲覧専用）</h1>

    <p><strong>氏名：</strong>{{ $application->candidate->name }}</p>
    <p><strong>メール：</strong>{{ $application->candidate->email }}</p>
    <p><strong>求人：</strong>{{ $application->job->title }}</p>

    <p style="margin-top:16px; color:#666;">
        ※ この画面は共有URLからの閲覧専用です
    </p>
@endsection
