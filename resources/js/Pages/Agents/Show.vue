<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    agent: Object,
    recommendations: Object,
    recommendationStats: Object,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const typeLabels = { human: '人材紹介', ai: 'AI' };

const statusLabels = {
    received: '受信',
    processing: '処理中',
    pending: '確認待ち',
    draft: 'ドラフト',
    confirmed: '確定',
    rejected: '却下',
    duplicate: '重複',
};

const statusColors = {
    confirmed: { bg: '#65b891' + '20', text: '#65b891' },
    rejected: { bg: '#ef444420', text: '#ef4444' },
    default: { bg: '#4e878c' + '20', text: '#4e878c' },
};

const getStatusColor = (status) => statusColors[status] || statusColors.default;
</script>

<template>
    <Head :title="`${agent.organization} - エージェント詳細`" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Link href="/agents" class="text-sm hover:underline" :style="{ color: colors.teal }">
                        ← エージェント一覧
                    </Link>
                </div>

                <!-- 2 Column Layout -->
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left: Agent Info -->
                    <div class="lg:w-80 flex-shrink-0">
                        <div class="lg:sticky lg:top-6">
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="px-6 py-5" :style="{ backgroundColor: colors.primary }">
                                    <h1 class="text-xl font-bold text-white">{{ agent.organization }}</h1>
                                    <p class="text-sm mt-1 opacity-80 text-white">{{ agent.name }}</p>
                                </div>

                                <!-- Badges -->
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                            :style="{ backgroundColor: colors.teal + '20', color: colors.teal }"
                                        >
                                            {{ typeLabels[agent.agent_type] }}
                                        </span>
                                        <span
                                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                            :style="agent.is_active
                                                ? { backgroundColor: colors.green + '20', color: colors.green }
                                                : { backgroundColor: '#ef444420', color: '#ef4444' }"
                                        >
                                            {{ agent.is_active ? '稼働中' : '停止中' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="px-6 py-4 space-y-3 border-b border-gray-100">
                                    <div v-if="agent.email">
                                        <dt class="text-xs font-medium text-gray-400">メール</dt>
                                        <dd class="text-sm" :style="{ color: colors.primary }">{{ agent.email }}</dd>
                                    </div>
                                    <div v-if="agent.phone">
                                        <dt class="text-xs font-medium text-gray-400">電話</dt>
                                        <dd class="text-sm" :style="{ color: colors.primary }">{{ agent.phone }}</dd>
                                    </div>
                                    <div v-if="agent.notes">
                                        <dt class="text-xs font-medium text-gray-400">メモ</dt>
                                        <dd class="text-sm text-gray-600">{{ agent.notes }}</dd>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <div class="grid grid-cols-3 gap-2 text-center">
                                        <div>
                                            <p class="text-lg font-bold" :style="{ color: colors.primary }">{{ recommendationStats.total }}</p>
                                            <p class="text-xs text-gray-400">総推薦</p>
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold" :style="{ color: colors.green }">{{ recommendationStats.confirmed }}</p>
                                            <p class="text-xs text-gray-400">確定</p>
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold text-red-500">{{ recommendationStats.rejected }}</p>
                                            <p class="text-xs text-gray-400">却下</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="px-6 py-5">
                                    <Link
                                        :href="`/agents/${agent.id}/edit`"
                                        class="block w-full px-5 py-2.5 text-sm font-semibold text-center border-2 rounded-lg transition-colors"
                                        :style="{ borderColor: colors.teal, color: colors.teal }"
                                    >
                                        編集する
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Recommendations -->
                    <div class="flex-1">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h2 class="text-lg font-semibold" :style="{ color: colors.primary }">
                                    推薦履歴
                                </h2>
                            </div>

                            <div v-if="recommendations.data.length === 0" class="p-12 text-center text-gray-500">
                                まだ推薦はありません
                            </div>

                            <div v-else class="divide-y divide-gray-100">
                                <Link
                                    v-for="rec in recommendations.data"
                                    :key="rec.id"
                                    :href="`/recommendations/${rec.id}`"
                                    class="block px-6 py-4 hover:bg-gray-50 transition-colors"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-sm font-semibold truncate" :style="{ color: colors.primary }">
                                                    {{ rec.candidate_data?.name || '名前なし' }}
                                                </h3>
                                                <span
                                                    class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                                                    :style="{ backgroundColor: getStatusColor(rec.status).bg, color: getStatusColor(rec.status).text }"
                                                >
                                                    {{ statusLabels[rec.status] || rec.status }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ rec.job?.title || '求人未指定' }}
                                                <span class="mx-1">·</span>
                                                {{ rec.received_at ? new Date(rec.received_at).toLocaleDateString('ja-JP') : '-' }}
                                            </p>
                                            <p v-if="rec.recommendation_comment" class="text-xs text-gray-400 mt-1 truncate">
                                                {{ rec.recommendation_comment }}
                                            </p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </Link>
                            </div>

                            <!-- Pagination -->
                            <div v-if="recommendations.links && recommendations.links.length > 3" class="px-6 py-4 border-t border-gray-100 flex justify-center">
                                <nav class="flex gap-1">
                                    <Link
                                        v-for="link in recommendations.links"
                                        :key="link.label"
                                        :href="link.url || '#'"
                                        :class="['px-3 py-1.5 text-xs rounded', link.active ? 'text-white' : 'bg-gray-100 text-gray-700', !link.url && 'opacity-50 cursor-not-allowed']"
                                        :style="link.active ? { backgroundColor: colors.primary } : {}"
                                        v-html="link.label"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
