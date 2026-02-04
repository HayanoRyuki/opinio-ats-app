<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    actions: Object,
    weeklyInterviews: Array,
    kpis: Object,
    funnel: Array,
    channelStats: Object,
    recentDrafts: Array,
});

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const channelLabels = {
    direct: '直接応募',
    scout: 'スカウト',
    agent: 'エージェント',
    referral: 'リファラル',
    media: 'スカウト',
};

// ファネルの最大値
const funnelMax = computed(() => {
    if (!props.funnel?.length) return 1;
    return Math.max(...props.funnel.map(f => f.count), 1);
});

// アクション合計
const totalActions = computed(() => {
    if (!props.actions) return 0;
    return props.actions.schedulePending + props.actions.evaluationPending + props.actions.draftPending + props.actions.longStagnant;
});
</script>

<template>
    <Head title="ダッシュボード" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                        ダッシュボード
                    </h1>
                    <p class="text-sm text-gray-500">採用活動全体の健康状態を一目で把握</p>
                </div>

                <!-- 今すぐのアクション -->
                <div
                    v-if="totalActions > 0"
                    class="mb-6 p-5 rounded-2xl shadow-lg"
                    :style="{ backgroundColor: colors.green }"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            今すぐのアクション
                        </h2>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-white text-sm font-medium">
                            {{ totalActions }}件
                        </span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <Link
                            v-if="actions.draftPending > 0"
                            href="/intake/drafts"
                            class="bg-white/10 hover:bg-white/20 rounded-xl p-4 transition-colors"
                        >
                            <p class="text-white/80 text-xs">確認待ちドラフト</p>
                            <p class="text-white text-2xl font-bold">{{ actions.draftPending }}</p>
                        </Link>
                        <div
                            v-if="actions.schedulePending > 0"
                            class="bg-white/10 rounded-xl p-4"
                        >
                            <p class="text-white/80 text-xs">日程調整待ち</p>
                            <p class="text-white text-2xl font-bold">{{ actions.schedulePending }}</p>
                        </div>
                        <div
                            v-if="actions.evaluationPending > 0"
                            class="bg-white/10 rounded-xl p-4"
                        >
                            <p class="text-white/80 text-xs">評価未入力</p>
                            <p class="text-white text-2xl font-bold">{{ actions.evaluationPending }}</p>
                        </div>
                        <div
                            v-if="actions.longStagnant > 0"
                            class="bg-white/10 rounded-xl p-4"
                        >
                            <p class="text-white/80 text-xs">長期滞留（14日+）</p>
                            <p class="text-white text-2xl font-bold">{{ actions.longStagnant }}</p>
                        </div>
                    </div>
                </div>

                <!-- KPIサマリー -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">今週の応募</p>
                                <p class="text-2xl font-bold" :style="{ color: colors.primary }">
                                    {{ kpis?.newApplications?.value || 0 }}
                                </p>
                            </div>
                            <span
                                v-if="kpis?.newApplications?.change !== 0"
                                :class="kpis?.newApplications?.change > 0 ? 'text-green-600' : 'text-red-600'"
                                class="text-sm font-medium"
                            >
                                {{ kpis?.newApplications?.change > 0 ? '+' : '' }}{{ kpis?.newApplications?.change }}
                            </span>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">選考中</p>
                                <p class="text-2xl font-bold" :style="{ color: colors.teal }">
                                    {{ kpis?.activeApplications?.value || 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">内定</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{ kpis?.offered?.value || 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">今週の入社</p>
                                <p class="text-2xl font-bold" :style="{ color: colors.green }">
                                    {{ kpis?.hired?.value || 0 }}
                                </p>
                            </div>
                            <span
                                v-if="kpis?.hired?.change !== 0"
                                :class="kpis?.hired?.change > 0 ? 'text-green-600' : 'text-red-600'"
                                class="text-sm font-medium"
                            >
                                {{ kpis?.hired?.change > 0 ? '+' : '' }}{{ kpis?.hired?.change }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- 選考ファネル -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold mb-4" :style="{ color: colors.primary }">
                            選考ファネル
                        </h2>
                        <div class="space-y-3">
                            <div
                                v-for="(stage, index) in funnel"
                                :key="stage.stage"
                                class="flex items-center gap-3"
                            >
                                <span class="text-sm text-gray-600 w-20">{{ stage.stage }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden">
                                    <div
                                        class="h-full rounded-lg flex items-center justify-end pr-2 transition-all"
                                        :style="{
                                            width: `${(stage.count / funnelMax) * 100}%`,
                                            backgroundColor: index === funnel.length - 1 ? colors.green : colors.teal,
                                            minWidth: stage.count > 0 ? '40px' : '0'
                                        }"
                                    >
                                        <span class="text-white text-sm font-medium">{{ stage.count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- チャネル別分析 -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold mb-4" :style="{ color: colors.primary }">
                            チャネル別候補者
                        </h2>
                        <div class="space-y-3">
                            <div
                                v-for="(count, channel) in channelStats"
                                :key="channel"
                                class="flex items-center justify-between p-3 rounded-lg"
                                :style="{ backgroundColor: colors.cream }"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-3 h-3 rounded-full"
                                        :class="{
                                            'bg-purple-500': channel === 'agent',
                                            'bg-orange-500': channel === 'referral',
                                        }"
                                        :style="channel === 'direct' ? { backgroundColor: colors.teal } : channel === 'scout' || channel === 'media' ? { backgroundColor: colors.green } : {}"
                                    ></div>
                                    <span class="text-sm font-medium" :style="{ color: colors.primary }">
                                        {{ channelLabels[channel] || channel }}
                                    </span>
                                </div>
                                <span class="text-lg font-bold" :style="{ color: colors.primary }">
                                    {{ count }}
                                </span>
                            </div>
                            <div v-if="Object.keys(channelStats || {}).length === 0" class="text-center text-gray-500 py-4">
                                データがありません
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 今週の面接 & 確認待ちドラフト -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- 今週の面接 -->
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="font-semibold" :style="{ color: colors.primary }">
                                今週の面接
                            </h2>
                        </div>
                        <div v-if="weeklyInterviews?.length > 0" class="divide-y divide-gray-100">
                            <div
                                v-for="interview in weeklyInterviews"
                                :key="interview.id"
                                class="px-6 py-4"
                            >
                                <!-- Interview item -->
                            </div>
                        </div>
                        <div v-else class="p-8 text-center text-gray-500">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm">今週の面接予定はありません</p>
                        </div>
                    </div>

                    <!-- 確認待ちドラフト -->
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-semibold" :style="{ color: colors.primary }">
                                確認待ちドラフト
                            </h2>
                            <Link
                                href="/intake/drafts"
                                class="text-sm hover:underline"
                                :style="{ color: colors.teal }"
                            >
                                すべて見る →
                            </Link>
                        </div>
                        <div v-if="recentDrafts?.length > 0" class="divide-y divide-gray-100">
                            <Link
                                v-for="draft in recentDrafts"
                                :key="draft.id"
                                :href="`/intake/drafts/${draft.id}`"
                                class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 transition-colors"
                            >
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center"
                                    :style="{ backgroundColor: colors.cream }"
                                >
                                    <svg class="w-5 h-5" :style="{ color: colors.teal }" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium truncate" :style="{ color: colors.primary }">{{ draft.name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ channelLabels[draft.application_intake?.channel] || draft.application_intake?.channel }}
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                        <div v-else class="p-8 text-center text-gray-500">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-sm">確認待ちのドラフトはありません</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
