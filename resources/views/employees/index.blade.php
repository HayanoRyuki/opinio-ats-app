@extends('layouts.ats')

@section('content')
<div style="padding:24px;">
    <h1 style="font-size:1.25rem; font-weight:600; margin-bottom:16px;">
        入社者一覧
    </h1>

    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th style="padding:8px; text-align:left;">候補者ID</th>
                <th style="padding:8px; text-align:left;">入社日</th>
                <th style="padding:8px; text-align:left;">在籍ステータス</th>
                <th style="padding:8px; text-align:left;">作成日時</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td style="padding:8px;">
                        {{ $employee->candidate_id }}
                    </td>
                    <td style="padding:8px;">
                        {{ $employee->joined_at }}
                    </td>
                    <td style="padding:8px;">
                        {{ $employee->status }}
                    </td>
                    <td style="padding:8px;">
                        {{ $employee->created_at }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
