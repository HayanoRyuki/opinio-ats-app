<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    intakes: Object,
    stats: Object,
});

const channelLabels = {
    direct: '直接応募',
    scout: 'スカウト',
    agent: 'エージェント',
    referral: 'リファラル',
    // 旧値（後方互換）
    media: 'スカウト',
};

const statusLabels = {
    received: '受信済み',
    processing: '処理中',
    pending: '未処理',
    draft: 'ドラフト',
    confirmed: '確定済み',
    rejected: '却下',
    duplicate: '重複',
};

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};
</script>

<template>
    <Head title="取り込み管理" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1
                        class="text-2xl font-bold"
                        :style="{ color: colors.primary }"
                    >
                        取り込み管理
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        各チャネルから取り込まれた応募データを管理します
                    </p>
                </div>

                <!-- Primary CTA: 確認待ちドラフト -->
                <Link
                    v-if="stats.draft > 0"
                    href="/intake/drafts"
                    class="block mb-8 p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1"
                    :style="{ backgroundColor: colors.green }"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white/80 text-sm font-medium">確認が必要です</p>
                                <p class="text-white text-xl font-bold">
                                    {{ stats.draft }}件のドラフトが確認待ち
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-white font-semibold">
                            確認する
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </Link>

                <!-- ドラフトがない場合のCTA -->
                <Link
                    v-else
                    href="/intake/drafts"
                    class="block mb-8 p-5 rounded-xl border-2 border-dashed transition-colors"
                    :style="{ borderColor: colors.teal + '50', backgroundColor: 'white' }"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center"
                                :style="{ backgroundColor: colors.cream }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.teal }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-gray-600">確認待ちのドラフトはありません</p>
                        </div>
                        <span class="text-sm" :style="{ color: colors.teal }">一覧を見る →</span>
                    </div>
                </Link>

                <!-- Stats Cards -->
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                style="background-color: #fef3c7"
                            >
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">未処理</p>
                                <p class="text-xl font-bold text-yellow-600">{{ stats.pending }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: colors.teal + '20' }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.teal }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">ドラフト</p>
                                <p class="text-xl font-bold" :style="{ color: colors.teal }">{{ stats.draft }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: colors.green + '20' }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.green }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">確定済み</p>
                                <p class="text-xl font-bold" :style="{ color: colors.green }">{{ stats.confirmed }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Intakes -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold" :style="{ color: colors.primary }">最近の取り込み</h2>
                    </div>

                    <div v-if="intakes.data.length === 0" class="p-12 text-center text-gray-500">
                        取り込みデータはありません
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <div
                            v-for="intake in intakes.data"
                            :key="intake.id"
                            class="px-6 py-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center gap-4">
                                <!-- Avatar -->
                                <div
                                    class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center"
                                    :style="{ backgroundColor: colors.cream }"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        :style="{ color: colors.teal }"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium truncate" :style="{ color: colors.primary }">
                                            {{ intake.draft?.name || intake.parsed_data?.name || '名前未取得' }}
                                        </span>
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                                            :style="{ backgroundColor: colors.teal + '20', color: colors.teal }"
                                        >
                                            {{ channelLabels[intake.channel] || intake.channel }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        {{ new Date(intake.received_at).toLocaleString('ja-JP') }}
                                    </p>
                                </div>

                                <!-- Status -->
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': intake.status === 'pending',
                                            'bg-green-100 text-green-800': intake.status === 'confirmed',
                                            'bg-red-100 text-red-800': intake.status === 'rejected',
                                        }"
                                        :style="intake.status === 'draft' ? { backgroundColor: colors.teal + '20', color: colors.teal } : {}"
                                    >
                                        {{ statusLabels[intake.status] || intake.status }}
                                    </span>

                                    <Link
                                        v-if="intake.draft"
                                        :href="`/intake/drafts/${intake.draft.id}`"
                                        class="text-sm font-medium hover:underline"
                                        :style="{ color: colors.teal }"
                                    >
                                        詳細 →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
