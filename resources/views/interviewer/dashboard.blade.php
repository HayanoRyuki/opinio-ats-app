@extends('layouts.ats')

@section('content')
    <h1 style="font-size:20px; font-weight:600; margin-bottom:16px;">
        今日の面接
    </h1>

    <p>
        {{ $user->name ?? '面接官' }} さん、今日予定されている面接は以下です。
    </p>

    <div style="margin-top:24px; padding:16px; border:1px solid #e5e7eb; border-radius:8px;">
        <p style="opacity:.6;">
            ※ ここに今日・明日の面接一覧が入ります（次のステップ）
        </p>
    </div>
@endsection
