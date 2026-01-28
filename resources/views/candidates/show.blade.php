@extends('layouts.ats')

@section('content')
<div class="p-6 bg-white space-y-8">

    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            候補者詳細
        </h1>
    </div>

    <div class="space-y-2">
        <div class="text-gray-500 text-sm">氏名</div>
        <div class="text-lg font-medium">{{ $candidate->name }}</div>

        <div class="text-gray-500 text-sm mt-4">メールアドレス</div>
        <div>{{ $candidate->email }}</div>

        @if ($candidate->phone)
        <div class="text-gray-500 text-sm mt-4">電話番号</div>
        <div>{{ $candidate->phone }}</div>
        @endif
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-3">
            応募している求人
        </h2>

        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="border-b bg-gray-50 text-left text-gray-600">
                    <th class="px-3 py-2">求人名</th>
                    <th class="px-3 py-2">ステータス</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $app)
                    <tr class="border-b">
                        <td class="px-3 py-3">
                            {{ $app->job?->title ?? '（求人情報なし）' }}
                        </td>
                        <td class="px-3 py-3">
                            {{ $app->status }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <a
            href="{{ route('candidates.index') }}"
            class="text-sm text-blue-600 hover:underline"
        >
            ← 候補者一覧に戻る
        </a>
    </div>

</div>
@endsection
