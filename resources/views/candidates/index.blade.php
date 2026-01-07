@extends('layouts.ats')

@section('content')
<div class="p-6 bg-white">

    {{-- ヘッダ --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            候補者一覧
        </h1>

        <a
            href="#"
            class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:underline"
        >
            ＋ 新規候補者追加
        </a>
    </div>

    {{-- 検索・フィルタ --}}
    <div class="flex flex-wrap items-center gap-3 mb-6 text-sm">
        <input
            type="text"
            placeholder="名前・メール・会社名"
            class="w-64 rounded border border-gray-300 px-3 py-1.5 focus:border-blue-500 focus:outline-none"
        >

        <select class="rounded border border-gray-300 px-2 py-1.5">
            <option>学歴</option>
            <option>高卒</option>
            <option>専門卒</option>
            <option>大卒</option>
            <option>院卒</option>
        </select>

        <select class="rounded border border-gray-300 px-2 py-1.5">
            <option>ステータス</option>
            <option>未選考</option>
            <option>書類選考中</option>
            <option>面接中</option>
            <option>内定</option>
            <option>不合格</option>
        </select>

        <label class="ml-2 flex items-center gap-1 text-gray-700">
            <input type="checkbox" class="rounded">
            重複候補者のみ
        </label>
    </div>

    {{-- 一覧テーブル --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="border-b bg-gray-50 text-left text-gray-600">
                    <th class="px-3 py-2 w-8"></th>
                    <th class="px-3 py-2">氏名</th>
                    <th class="px-3 py-2">年齢</th>
                    <th class="px-3 py-2">学歴</th>
                    <th class="px-3 py-2">経験社数</th>
                    <th class="px-3 py-2">現職</th>
                    <th class="px-3 py-2">ステータス</th>
                    <th class="px-3 py-2 w-8"></th>
                </tr>
            </thead>

            <tbody>
                {{-- 候補者 1 --}}
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-3 py-3">
                        <input type="checkbox">
                    </td>

                    <td class="px-3 py-3">
                        <a href="#" class="font-medium text-blue-600 hover:underline">
                            山田 太郎
                        </a>
                        <div class="text-xs text-gray-500">
                            yamada@example.com
                        </div>
                    </td>

                    <td class="px-3 py-3">29</td>
                    <td class="px-3 py-3">大卒</td>
                    <td class="px-3 py-3">3</td>
                    <td class="px-3 py-3">株式会社サンプル</td>

                    <td class="px-3 py-3">
                        <span class="inline-flex items-center gap-1 rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                            書類選考中
                        </span>
                    </td>

                    <td class="px-3 py-3 text-yellow-500" title="重複候補者の可能性">
                        △
                    </td>
                </tr>

                {{-- 候補者 2 --}}
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-3 py-3">
                        <input type="checkbox">
                    </td>

                    <td class="px-3 py-3">
                        <a href="#" class="font-medium text-blue-600 hover:underline">
                            佐藤 花子
                        </a>
                        <div class="text-xs text-gray-500">
                            sato@example.com
                        </div>
                    </td>

                    <td class="px-3 py-3">34</td>
                    <td class="px-3 py-3">院卒</td>
                    <td class="px-3 py-3">5</td>
                    <td class="px-3 py-3">株式会社テック</td>

                    <td class="px-3 py-3">
                        <span class="inline-flex items-center rounded bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700">
                            未選考
                        </span>
                    </td>

                    <td class="px-3 py-3"></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ページネーション（ダミー） --}}
    <div class="mt-4 text-sm text-gray-600">
        1 / 1
    </div>

</div>
@endsection
