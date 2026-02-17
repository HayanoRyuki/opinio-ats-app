<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    recommendations: Object,
    agents: Array,
    filters: Object,
    stats: Object,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const currentStatus = ref(props.filters?.status || '');
const currentAgentId = ref(props.filters?.agent_id || '');
const currentSort = ref(props.filters?.sort || 'newest');

const applyFilter = () => {
    router.get('/recommendations', {
        status: currentStatus.value || undefined,
        agent_id: currentAgentId.value || undefined,
        sort: currentSort.value,
    }, { preserveState: true, preserveScroll: true });
};

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

const statusOptions = [
    { value: '', label: 'すべて' },
    { value: 'pending', label: '確認待ち' },
    { value: 'draft', label: 'ドラフト' },
    { value: 'confirmed', label: '確定' },
    { value: 'rejected', label: '却下' },
];
</script>

<template>
    <Head title="推薦管理" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">推薦管理</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ stats.pending }}件が確認待ち（全{{ stats.total }}件）
                        </p>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-4 gap-3 mb-4">
                    <div class="bg-white rounded-xl shadow p-4 text-center">
                        <p class="text-2xl font-bold" :style="{ color: colors.primary }">{{ stats.total }}</p>
                        <p class="text-xs text-gray-400 mt-1">全推薦</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 text-center">
                        <p class="text-2xl font-bold" :style="{ color: colors.teal }">{{ stats.pending }}</p>
                        <p class="text-xs text-gray-400 mt-1">確認待ち</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 text-center">
                        <p class="text-2xl font-bold" :style="{ color: colors.green }">{{ stats.confirmed }}</p>
                        <p class="text-xs text-gray-400 mt-1">確定</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 text-center">
                        <p class="text-2xl font-bold text-red-500">{{ stats.rejected }}</p>
                        <p class="text-xs text-gray-400 mt-1">却下</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <select
                            v-model="currentStatus"
                            @change="applyFilter"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2"
                        >
                            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                        <select
                            v-model="currentAgentId"
                            @change="applyFilter"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2"
                        >
                            <option value="">全エージェント</option>
                            <option v-for="agent in agents" :key="agent.id" :value="agent.id">
                                {{ agent.organization }} / {{ agent.name }}
                            </option>
                        </select>
                        <div class="flex-1"></div>
                        <select
                            v-model="currentSort"
                            @change="applyFilter"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2"
                        >
                            <option value="newest">新しい順</option>
                            <option value="oldest">古い順</option>
                        </select>
                    </div>
                </div>

                <!-- Recommendation List -->
                <div class="space-y-3">
                    <div v-if="recommendations.data.length === 0" class="bg-white rounded-xl shadow p-12 text-center">
                        <p class="text-gray-500">該当する推薦はありません</p>
                    </div>

                    <Link
                        v-for="rec in recommendations.data"
                        :key="rec.id"
                        :href="`/recommendations/${rec.id}`"
                        class="block bg-white rounded-xl shadow hover:shadow-lg transition-shadow p-4"
                    >
                        <div class="flex items-center gap-4">
                            <!-- Avatar -->
                            <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center" :style="{ backgroundColor: colors.cream }">
                                <svg class="w-6 h-6" :style="{ color: colors.teal }" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold truncate" :style="{ color: colors.primary }">
                                        {{ rec.candidate_data?.name || '名前なし' }}
                                    </h3>
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full flex-shrink-0"
                                        :style="{ backgroundColor: getStatusColor(rec.status).bg, color: getStatusColor(rec.status).text }"
                                    >
                                        {{ statusLabels[rec.status] || rec.status }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ rec.agent?.organization || rec.agent_company_name || '不明' }}
                                    / {{ rec.agent?.name || rec.agent_name || '-' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ rec.job?.title || '求人未指定' }}
                                    <span class="mx-1">·</span>
                                    {{ rec.received_at ? new Date(rec.received_at).toLocaleDateString('ja-JP') : '-' }}
                                </p>
                            </div>

                            <!-- Linked candidates -->
                            <div v-if="rec.links?.length > 0" class="flex-shrink-0">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :style="{ backgroundColor: colors.green + '20', color: colors.green }">
                                    紐付済
                                </span>
                            </div>

                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="recommendations.links && recommendations.links.length > 3" class="mt-6 flex justify-center">
                    <nav class="flex gap-1">
                        <Link
                            v-for="link in recommendations.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="['px-3 py-2 text-sm rounded', link.active ? 'text-white' : 'bg-white text-gray-700 hover:bg-gray-50', !link.url && 'opacity-50 cursor-not-allowed']"
                            :style="link.active ? { backgroundColor: colors.primary } : {}"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
