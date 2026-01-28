<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    auth: Object,
});
</script>

<template>
    <Head title="ダッシュボード" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    ダッシュボード
                </h1>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="card p-6">
                        <div class="text-sm font-medium text-gray-500">
                            未処理の取り込み
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">
                            0
                        </div>
                    </div>
                    <div class="card p-6">
                        <div class="text-sm font-medium text-gray-500">
                            候補者数
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">
                            0
                        </div>
                    </div>
                    <div class="card p-6">
                        <div class="text-sm font-medium text-gray-500">
                            公開中の求人
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">
                            0
                        </div>
                    </div>
                    <div class="card p-6">
                        <div class="text-sm font-medium text-gray-500">
                            進行中の応募
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">
                            0
                        </div>
                    </div>
                </div>

                <!-- Pending Intakes -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            確認待ちの候補者ドラフト
                        </h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 text-center py-8">
                            確認待ちの候補者はありません
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
