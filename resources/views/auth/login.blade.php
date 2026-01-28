@extends('layouts.ats')

@php
$description = 'ログインしてOpinio ATSの管理画面にアクセスしてください。';
@endphp

@section('content')
<div style="
    min-height:calc(100vh - 48px);
    display:flex;
    align-items:center;
    justify-content:center;
">
    <div style="
        width:100%;
        max-width:360px;
        background:#ffffff;
        padding:32px;
        border-radius:8px;
        box-shadow:0 10px 30px rgba(0,0,0,0.08);
    ">
        <h1 style="
            font-size:1.2rem;
            font-weight:600;
            margin-bottom:20px;
            text-align:center;
        ">
            ログイン
        </h1>

        <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:14px;">
            @csrf

            <input
                type="email"
                name="email"
                placeholder="メールアドレス"
                required
                style="padding:10px; border:1px solid #ccc; border-radius:4px;"
            >

            <input
                type="password"
                name="password"
                placeholder="パスワード"
                required
                style="padding:10px; border:1px solid #ccc; border-radius:4px;"
            >

            <button
                type="submit"
                style="
                    margin-top:8px;
                    padding:10px;
                    background:#111827;
                    color:#fff;
                    border:none;
                    border-radius:4px;
                    cursor:pointer;
                "
            >
                ログイン
            </button>
        </form>
    </div>
</div>
@endsection
