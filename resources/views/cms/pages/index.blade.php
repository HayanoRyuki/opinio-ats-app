@extends('layouts.menu')

@section('content')
<div class="max-w-5xl mx-auto p-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">求人ページ一覧</h1>

        <a href="{{ route('cms.pages.create') }}"
           class="px-4 py-2 bg-emerald-600 text-white rounded">
            ＋ 求人ページ作成
        </a>
    </div>

    @if($pages->isEmpty())
        <div class="bg-white p-6 rounded text-gray-500">
            まだ求人ページが作成されていません。
        </div>
    @else
        <div class="bg-white rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">タイトル</th>
                        <th class="p-3 text-left">紐づく求人</th>
                        <th class="p-3 text-left">ステータス</th>
                        <th class="p-3 text-left">公開URL</th>
                        <th class="p-3 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($pages as $page)
                    <tr class="border-t">
                        <td class="p-3 font-medium">
                            {{ $page->title }}
                        </td>

                        <td class="p-3">
                            @if($page->job)
                                {{ $page->job->title }}
                            @else
                                <span class="text-gray-400">なし</span>
                            @endif
                        </td>

                        <td class="p-3">
                            @if($page->status === 'published')
                                <span class="text-green-600 font-semibold">公開</span>
                            @else
                                <span class="text-gray-500">下書き</span>
                            @endif
                        </td>

                        <td class="p-3">
                            @if($page->status === 'published')
                                <input
                                    type="text"
                                    readonly
                                    value="{{ url('/jobs/'.$page->slug) }}"
                                    class="border px-2 py-1 text-xs w-full"
                                    onclick="this.select();">
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="p-3 text-right">
                            <a href="{{ route('cms.pages.edit', $page) }}"
                               class="text-blue-600 hover:underline">
                                編集
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection
