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

    {{-- 今後ここにダッシュボード要素を追加 --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">

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
