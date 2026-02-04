<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    interviews: Array,
    filter: String,
    stats: Object,
});

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

// フィルタ変更
const changeFilter = (newFilter) => {
    router.get('/interviews', { filter: newFilter }, { preserveState: true });
};

// 経過日数を計算
const getDaysAgo = (date) => {
    const now = new Date();
    const updated = new Date(date);
    const diff = Math.floor((now - updated) / (1000 * 60 * 60 * 24));
    if (diff === 0) return '今日';
    if (diff === 1) return '昨日';
    return `${diff}日前`;
};

// フィルタオプション
const filterOptions = [
    { value: 'all', label: 'すべて' },
    { value: 'this_week', label: '今週更新' },
    { value: 'overdue', label: '要対応（7日+）' },
];
</script>

<template>
    <Head title="面接管理" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                            面接管理
                        </h1>
                        <p class="text-sm text-gray-500">選考中の候補者を管理</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-5">
                        <p class="text-xs text-gray-500 mb-1">選考中</p>
                        <p class="text-2xl font-bold" :style="{ color: colors.primary }">
                            {{ stats?.total || 0 }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <p class="text-xs text-gray-500 mb-1">今週更新</p>
                        <p class="text-2xl font-bold" :style="{ color: colors.teal }">
                            {{ stats?.thisWeek || 0 }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <p class="text-xs text-gray-500 mb-1">要対応（7日+）</p>
                        <p class="text-2xl font-bold" :class="stats?.overdue > 0 ? 'text-red-500' : 'text-gray-400'">
                            {{ stats?.overdue || 0 }}
                        </p>
                    </div>
                </div>

                <!-- Filter -->
                <div class="flex gap-2 mb-6">
                    <button
                        v-for="opt in filterOptions"
                        :key="opt.value"
                        @click="changeFilter(opt.value)"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                        :style="filter === opt.value ? {
                            backgroundColor: colors.teal,
                            color: 'white'
                        } : {
                            backgroundColor: 'white',
                            color: colors.primary
                        }"
                    >
                        {{ opt.label }}
                    </button>
                </div>

                <!-- Interview List -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold" :style="{ color: colors.primary }">
                            選考中の候補者
                        </h2>
                    </div>

                    <div v-if="interviews?.length > 0" class="divide-y divide-gray-100">
                        <div
                            v-for="item in interviews"
                            :key="item.id"
                            class="p-5 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <!-- Avatar -->
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center"
                                        :style="{ backgroundColor: colors.cream }"
                                    >
                                        <svg class="w-6 h-6" :style="{ color: colors.teal }" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>

                                    <!-- Info -->
                                    <div>
                                        <Link
                                            :href="`/candidates/${item.candidate_id}`"
                                            class="font-medium hover:underline"
                                            :style="{ color: colors.primary }"
                                        >
                                            {{ item.candidate?.name || '名前なし' }}
                                        </Link>
                                        <p class="text-sm text-gray-500">
                                            {{ item.job?.title || '求人なし' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <!-- 経過日数 -->
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">最終更新</p>
                                        <p
                                            class="text-sm font-medium"
                                            :class="getDaysAgo(item.updated_at).includes('日前') && parseInt(getDaysAgo(item.updated_at)) >= 7 ? 'text-red-500' : 'text-gray-700'"
                                        >
                                            {{ getDaysAgo(item.updated_at) }}
                                        </p>
                                    </div>

                                    <!-- Status Badge -->
                                    <span
                                        class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                        :style="{ backgroundColor: colors.teal + '20', color: colors.teal }"
                                    >
                                        選考中
                                    </span>

                                    <!-- Action -->
                                    <Link
                                        :href="`/applications/${item.id}`"
                                        class="text-sm hover:underline"
                                        :style="{ color: colors.teal }"
                                    >
                                        詳細 →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500 mb-2">選考中の候補者がいません</p>
                        <p class="text-sm text-gray-400">候補者を登録すると、ここに表示されます</p>
                    </div>
                </div>

                <!-- Future Feature Notice -->
                <div class="mt-6 p-4 rounded-lg bg-white/50 border border-gray-200">
                    <p class="text-sm text-gray-500 text-center flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        カレンダー連携・面接日時の設定機能は今後追加予定です
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
