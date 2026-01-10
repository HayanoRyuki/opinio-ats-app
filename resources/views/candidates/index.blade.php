@extends('layouts.ats')

@section('content')
<div class="p-6 bg-white">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            候補者一覧
        </h1>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="border-b bg-gray-50 text-left text-gray-600">
                    <th class="px-3 py-2">氏名</th>
                    <th class="px-3 py-2">メールアドレス</th>
                    <th class="px-3 py-2">ステータス</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $app)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-3 py-3">
                            <a
                                href="{{ route('candidates.show', $app->candidate->id) }}"
                                class="font-medium text-blue-600 hover:underline"
                            >
                                {{ $app->candidate->name }}
                            </a>
                        </td>
                        <td class="px-3 py-3 text-gray-700">
                            {{ $app->candidate->email }}
                        </td>
                        <td class="px-3 py-3 text-gray-600">
                            {{ $app->status }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-3 py-6 text-center text-gray-500">
                            応募者がいません
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
