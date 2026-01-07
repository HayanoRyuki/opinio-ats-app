@extends('layouts.ats')

@section('content')
<div class="p-6">

    {{-- ページヘッダ --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">
            候補者一覧
        </h1>

        <a href="#" class="btn-primary">
            ＋ 新規候補者追加
        </a>
    </div>

    {{-- 検索・フィルタ --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <input
            type="text"
            class="input w-64"
            placeholder="名前・メール・会社名で検索"
        >

        <select class="select">
            <option>学歴</option>
            <option>高卒</option>
            <option>専門卒</option>
            <option>大卒</option>
            <option>院卒</option>
        </select>

        <select class="select">
            <option>ステータス</option>
            <option>未選考</option>
            <option>書類選考中</option>
            <option>面接中</option>
            <option>内定</option>
            <option>不合格</option>
        </select>

        <label class="flex items-center gap-1 text-sm text-gray-700">
            <input type="checkbox">
            重複候補者のみ
        </label>
    </div>

    {{-- 一覧テーブル --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="py-2 px-2 w-8"></th>
                    <th class="py-2 px-2">氏名</th>
                    <th class="py-2 px-2">年齢</th>
                    <th class="py-2 px-2">学歴</th>
                    <th class="py-2 px-2">経験社数</th>
                    <th class="py-2 px-2">現職</th>
                    <th class="py-2 px-2">ステータス</th>
                    <th class="py-2 px-2 w-8"></th>
                </tr>
            </thead>
            <tbody>

                {{-- ダミー行 --}}
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-2">
                        <input type="checkbox">
                    </td>
                    <td class="py-2 px-2">
                        <a href="#" class="font-medium text-blue-600 hover:underline">
                            山田 太郎
                        </a>
                        <div class="text-xs text-gray-500">
                            yamada@example.com
                        </div>
                    </td>
                    <td class="py-2 px-2">29</td>
                    <td class="py-2 px-2">大卒</td>
                    <td class="py-2 px-2">3</td>
                    <td class="py-2 px-2">株式会社サンプル</td>
                    <td class="py-2 px-2">
                        <span class="inline-block px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                            書類選考中
                        </span>
                    </td>
                    <td class="py-2 px-2 text-yellow-500" title="重複候補者の可能性">
                        ⚠
                    </td>
                </tr>

                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-2">
                        <input type="checkbox">
                    </td>
                    <td class="py-2 px-2">
                        <a href="#" class="font-medium text-blue-600 hover:underline">
                            佐藤 花子
                        </a>
                        <div class="text-xs text-gray-500">
                            sato@example.com
                        </div>
                    </td>
                    <td class="py-2 px-2">34</td>
                    <td class="py-2 px-2">院卒</td>
                    <td class="py-2 px-2">5</td>
                    <td class="py-2 px-2">株式会社テック</td>
                    <td class="py-2 px-2">
                        <span class="inline-block px-2 py-1 text-xs rounded bg-gray-200 text-gray-700">
                            未選考
                        </span>
                    </td>
                    <td class="py-2 px-2"></td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- ページネーション（ダミー） --}}
    <div class="flex justify-end mt-4 text-sm text-gray-600">
        1 / 1
    </div>

</div>
@endsection
