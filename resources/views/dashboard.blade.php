{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.menu')

@section('content')
<div class="max-w-5xl mx-auto p-6">

    {{-- ページタイトル --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">マイページ</h1>
        <p class="text-sm text-gray-500 mt-1">
            Opinio ATS の初期設定と進捗状況を確認できます。
        </p>
    </div>

    {{-- Onboarding ガイド --}}
    @if(isset($onboarding))
        @include('components.onboarding-guide', ['onboarding' => $onboarding])
    @endif

    {{-- ダッシュボード --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- 応募者ステータス構成（予定） --}}
        <div class="border rounded p-4 bg-gray-50">
            <h2 class="font-bold mb-2">
                応募者ステータス構成（準備中）
            </h2>

            <p class="text-sm text-gray-600 mb-4">
                今後、全求人・全応募者を対象にした
                <strong>応募状況の構成グラフ</strong>を表示します。
            </p>

            <div class="text-sm text-gray-500 leading-relaxed">
                例：
                <ul class="list-disc pl-5 mt-2">
                    <li>書類応募済み</li>
                    <li>面接待ち</li>
                    <li>最終面接待ち</li>
                    <li>内定・入社手続き待ち</li>
                </ul>
            </div>

            <div class="mt-4 text-xs text-gray-400">
                ※ 今後、円グラフ形式で可視化予定
            </div>
        </div>

        <div class="border rounded p-4 bg-gray-50">
    <h2 class="font-bold mb-2">
        応募ステータス構成（職種別）
    </h2>

    <p class="text-sm text-gray-600 mb-3">
        全応募者のうち、職種別に「書類応募・面接待ち・最終面接・入社手続き」の
        構成比を可視化します。（上位3職種を表示予定）
    </p>

    <p class="text-xs text-gray-400">
        ※ 後日、円グラフ形式で実装予定
    </p>
</div>

<div class="border rounded p-4 bg-gray-50">
    <h2 class="font-bold mb-2">
        月別 応募数・職種構成
    </h2>

    <p class="text-sm text-gray-600 mb-3">
        月ごとの応募数と、その内訳（職種別割合）を帯グラフで表示します。
        採用活動の推移や職種ごとの変化を確認できます。
    </p>

    <p class="text-xs text-gray-400">
        ※ 後日、構成グラフとして実装予定
    </p>
</div>


        {{-- 求人管理ショートカット --}}
        <div class="border rounded p-4 bg-white">
            <h2 class="font-bold mb-2">求人管理</h2>
            <p class="text-sm text-gray-600 mb-4">
                登録済みの求人一覧や、新規求人の作成はこちらから。
            </p>

            <a href="{{ route('jobs.index') }}"
               class="inline-block px-4 py-2 bg-blue-600 text-white rounded text-sm">
                求人一覧を見る
            </a>
        </div>

        {{-- 職種管理ショートカット --}}
        <div class="border rounded p-4 bg-white">
            <h2 class="font-bold mb-2">職種管理</h2>
            <p class="text-sm text-gray-600 mb-4">
                求人作成の前提となる職種を管理します。
            </p>

            <a href="{{ route('ats.job_roles.index') }}"
               class="inline-block px-4 py-2 bg-blue-600 text-white rounded text-sm">
                職種を管理する
            </a>
        </div>

    </div>

</div>
@endsection
