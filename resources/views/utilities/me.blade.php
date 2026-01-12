@extends('layouts.ats')

@section('content')
@php
    $role = request()->attributes->get('role');
    $companyId = request()->attributes->get('company_id');
    $userId = request()->attributes->get('auth_user_id');

    $authAppUrl = config('services.auth.url') ?? env('AUTH_APP_URL');
@endphp

<div style="max-width:720px;">
    <h1 style="font-size:20px; font-weight:700; margin-bottom:16px;">
        マイページ
    </h1>

    <div style="
        background:#ffffff;
        border-radius:8px;
        padding:16px;
        box-shadow:0 1px 4px rgba(0,0,0,0.08);
        margin-bottom:16px;
    ">
        <table style="width:100%; border-collapse:collapse;">
            <tbody>
                <tr>
                    <th style="text-align:left; padding:8px; width:180px;">role</th>
                    <td style="padding:8px;">{{ $role ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="text-align:left; padding:8px;">company_id</th>
                    <td style="padding:8px;">{{ $companyId ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="text-align:left; padding:8px;">user_id</th>
                    <td style="padding:8px;">{{ $userId ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <a href="{{ rtrim($authAppUrl, '/') }}/logout"
       style="
            display:inline-block;
            background:#e5533d;
            color:#fff;
            padding:10px 16px;
            border-radius:6px;
            text-decoration:none;
            font-weight:600;
       ">
        ログアウト
    </a>
</div>
@endsection
