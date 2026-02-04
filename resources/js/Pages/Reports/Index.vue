<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    period: String,
    periodLabel: String,
    startDate: String,
    endDate: String,
    pipeline: Object,
    channelAnalysis: Array,
    monthlyTrend: Array,
    jobSummary: Array,
});

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

// 期間オプション
const periodOptions = [
    { value: 'this_month', label: '今月' },
    { value: 'last_month', label: '先月' },
    { value: 'this_quarter', label: '今四半期' },
    { value: 'last_quarter', label: '前四半期' },
    { value: 'this_year', label: '今年' },
    { value: 'all', label: '全期間' },
];

// 期間変更
const changePeriod = (newPeriod) => {
    router.get('/reports', { period: newPeriod }, { preserveState: true });
};

// 月別推移の最大値（グラフ用）
const trendMax = computed(() => {
    if (!props.monthlyTrend?.length) return 1;
    return Math.max(...props.monthlyTrend.map(m => m.applications), 1);
});

// チャネル分析の最大値（グラフ用）
const channelMax = computed(() => {
    if (!props.channelAnalysis?.length) return 1;
    return Math.max(...props.channelAnalysis.map(c => c.applications), 1);
});
</script>

<template>
    <Head title="レポート" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                            レポート
                        </h1>
                        <p class="text-sm text-gray-500">採用活動の分析・振り返り</p>
                    </div>

                    <!-- 期間セレクター -->
                    <div class="mt-4 sm:mt-0 flex items-center gap-2">
                        <span class="text-sm text-gray-500">期間:</span>
                        <select
                            :value="period"
                            @change="changePeriod($event.target.value)"
                            class="rounded-lg border-gray-200 text-sm py-2 px-3 focus:ring-2 focus:border-transparent"
                            :style="{ '--tw-ring-color': colors.teal }"
                        >
                            <option
                                v-for="opt in periodOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- パイプラインサマリー -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-5">
                        <p class="text-xs text-gray-500 mb-1">応募数</p>
                        <p class="text-2xl font-bold" :style="{ color: colors.primary }">
                            {{ pipeline?.applications?.value || 0 }}
                        </p>
                        <p
                            v-if="pipeline?.applications?.change !== 0"
                            :class="pipeline?.applications?.change > 0 ? 'text-green-600' : 'text-red-600'"
                            class="text-xs mt-1"
                        >
                            前期比 {{ pipeline?.applications?.change > 0 ? '+' : '' }}{{ pipeline?.applications?.change }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <p class="text-xs text-gray-500 mb-1">内定</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ pipeline?.offered?.value || 0 }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <p class="text-xs text-gray-500 mb-1">入社</p>
                        <p class="text-2xl font-bold" :style="{ color: colors.green }">
                            {{ pipeline?.hired?.value || 0 }}
                        </p>
                        <p
                            v-if="pipeline?.hired?.change !== 0"
                            :class="pipeline?.hired?.change > 0 ? 'text-green-600' : 'text-red-600'"
                            class="text-xs mt-1"
                        >
                            前期比 {{ pipeline?.hired?.change > 0 ? '+' : '' }}{{ pipeline?.hired?.change }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <p class="text-xs text-gray-500 mb-1">不採用</p>
                        <p class="text-2xl font-bold text-gray-600">
                            {{ pipeline?.rejected?.value || 0 }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <p class="text-xs text-gray-500 mb-1">内定通過率</p>
                        <p class="text-2xl font-bold" :style="{ color: colors.teal }">
                            {{ pipeline?.passRate || 0 }}%
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- 月別推移グラフ -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold mb-4" :style="{ color: colors.primary }">
                            月別推移（過去6ヶ月）
                        </h2>
                        <div class="flex items-end justify-between h-48 gap-2">
                            <div
                                v-for="month in monthlyTrend"
                                :key="month.month"
                                class="flex-1 flex flex-col items-center"
                            >
                                <div class="w-full flex flex-col items-center justify-end h-36">
                                    <!-- 応募バー -->
                                    <div
                                        class="w-full max-w-[40px] rounded-t transition-all relative group"
                                        :style="{
                                            height: `${(month.applications / trendMax) * 100}%`,
                                            backgroundColor: colors.teal,
                                            minHeight: month.applications > 0 ? '8px' : '0'
                                        }"
                                    >
                                        <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            応募: {{ month.applications }}
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">{{ month.monthShort }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-6 mt-4 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded" :style="{ backgroundColor: colors.teal }"></div>
                                <span class="text-gray-600">応募</span>
                            </div>
                        </div>
                    </div>

                    <!-- チャネル効果分析 -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-semibold mb-4" :style="{ color: colors.primary }">
                            チャネル効果分析
                        </h2>
                        <div class="space-y-4">
                            <div
                                v-for="channel in channelAnalysis"
                                :key="channel.channel"
                                class="flex items-center gap-4"
                            >
                                <div class="w-24 text-sm font-medium" :style="{ color: colors.primary }">
                                    {{ channel.label }}
                                </div>
                                <div class="flex-1">
                                    <div class="h-6 bg-gray-100 rounded-lg overflow-hidden">
                                        <div
                                            class="h-full rounded-lg flex items-center justify-end pr-2 transition-all"
                                            :style="{
                                                width: `${(channel.applications / channelMax) * 100}%`,
                                                backgroundColor: colors.teal,
                                                minWidth: channel.applications > 0 ? '30px' : '0'
                                            }"
                                        >
                                            <span class="text-white text-xs font-medium">{{ channel.applications }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-16 text-right">
                                    <span
                                        class="text-sm font-medium"
                                        :class="channel.passRate >= 30 ? 'text-green-600' : channel.passRate >= 10 ? 'text-yellow-600' : 'text-gray-500'"
                                    >
                                        {{ channel.passRate }}%
                                    </span>
                                </div>
                            </div>
                            <div v-if="!channelAnalysis?.length" class="text-center text-gray-500 py-4">
                                データがありません
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 mt-4 text-xs text-gray-500">
                            <span>応募数</span>
                            <span>|</span>
                            <span>通過率</span>
                        </div>
                    </div>
                </div>

                <!-- 求人別サマリー -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold" :style="{ color: colors.primary }">
                            求人別サマリー
                        </h2>
                    </div>
                    <div v-if="jobSummary?.length > 0" class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        求人
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        応募
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        選考中
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        入社
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="job in jobSummary"
                                    :key="job.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="px-6 py-4">
                                        <Link
                                            :href="`/jobs/${job.id}`"
                                            class="font-medium hover:underline"
                                            :style="{ color: colors.primary }"
                                        >
                                            {{ job.title }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-900">
                                        {{ job.applications }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span :style="{ color: colors.teal }">{{ job.active }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span :style="{ color: colors.green }">{{ job.hired }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="p-8 text-center text-gray-500">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm">求人データがありません</p>
                    </div>
                </div>

                <!-- フッター -->
                <div class="mt-6 text-center text-xs text-gray-400">
                    集計期間: {{ startDate }} 〜 {{ endDate }}
                </div>
            </div>
        </div>
    </AppLayout>
</template>
