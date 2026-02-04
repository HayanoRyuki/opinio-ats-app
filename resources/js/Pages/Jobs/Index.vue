<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    jobs: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const statusLabels = {
    draft: '下書き',
    open: '募集中',
    paused: '一時停止',
    closed: '募集終了',
};

const statusColors = {
    draft: { bg: '#9ca3af20', color: '#6b7280' },
    open: { bg: '#65b89120', color: '#65b891' },
    paused: { bg: '#f59e0b20', color: '#f59e0b' },
    closed: { bg: '#ef444420', color: '#ef4444' },
};

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const applyFilters = () => {
    router.get('/jobs', {
        search: search.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const setStatus = (st) => {
    status.value = st;
    applyFilters();
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    router.get('/jobs');
};

let searchTimeout;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});
</script>

<template>
    <Head title="求人管理" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                            求人管理
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            求人票の作成・管理を行います
                        </p>
                    </div>
                    <Link
                        href="/jobs/create"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-white font-medium shadow-sm hover:opacity-90 transition-opacity"
                        :style="{ backgroundColor: colors.green }"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        新規求人作成
                    </Link>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-4 gap-4 mb-8">
                    <button
                        @click="setStatus('')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': status === '' }"
                        :style="status === '' ? { '--tw-ring-color': colors.primary } : {}"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: colors.primary + '15' }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">全体</p>
                                <p class="text-xl font-bold" :style="{ color: colors.primary }">{{ stats.total }}</p>
                            </div>
                        </div>
                    </button>

                    <button
                        @click="setStatus('open')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': status === 'open' }"
                        :style="status === 'open' ? { '--tw-ring-color': colors.green } : {}"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: colors.green + '20' }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.green }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">募集中</p>
                                <p class="text-xl font-bold" :style="{ color: colors.green }">{{ stats.open }}</p>
                            </div>
                        </div>
                    </button>

                    <button
                        @click="setStatus('draft')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': status === 'draft' }"
                        style="--tw-ring-color: #6b7280"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">下書き</p>
                                <p class="text-xl font-bold text-gray-600">{{ stats.draft }}</p>
                            </div>
                        </div>
                    </button>

                    <button
                        @click="setStatus('closed')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': status === 'closed' }"
                        style="--tw-ring-color: #ef4444"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-red-50">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">募集終了</p>
                                <p class="text-xl font-bold text-red-500">{{ stats.closed }}</p>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Search -->
                <div class="bg-white rounded-xl shadow p-4 mb-6">
                    <div class="flex gap-4 items-center">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="求人タイトルで検索..."
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2"
                                :style="{ '--tw-ring-color': colors.teal }"
                            />
                        </div>
                        <button
                            v-if="search || status"
                            @click="clearFilters"
                            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                            :style="{ color: colors.primary }"
                        >
                            クリア
                        </button>
                    </div>
                </div>

                <!-- Jobs List -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div v-if="jobs.data.length === 0" class="p-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <p>求人がありません</p>
                        <Link
                            href="/jobs/create"
                            class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg text-white font-medium"
                            :style="{ backgroundColor: colors.green }"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            最初の求人を作成
                        </Link>
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <div
                            v-for="job in jobs.data"
                            :key="job.id"
                            class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors"
                        >
                            <!-- Icon -->
                            <div
                                class="w-12 h-12 rounded-lg flex-shrink-0 flex items-center justify-center"
                                :style="{
                                    backgroundColor: statusColors[job.status]?.bg || colors.cream,
                                }"
                            >
                                <svg
                                    class="w-6 h-6"
                                    :style="{ color: statusColors[job.status]?.color || colors.teal }"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                                </svg>
                            </div>

                            <!-- Info -->
                            <Link :href="`/jobs/${job.id}`" class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold truncate" :style="{ color: colors.primary }">
                                        {{ job.title }}
                                    </span>
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full flex-shrink-0"
                                        :style="{
                                            backgroundColor: statusColors[job.status]?.bg || '#e5e7eb',
                                            color: statusColors[job.status]?.color || '#6b7280',
                                        }"
                                    >
                                        {{ statusLabels[job.status] || job.status }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span>応募: {{ job.applications_count }} 件</span>
                                    <span>作成日: {{ new Date(job.created_at).toLocaleDateString('ja-JP') }}</span>
                                </div>
                            </Link>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <Link
                                    :href="`/jobs/${job.id}/edit`"
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                                    :style="{ color: colors.primary }"
                                >
                                    編集
                                </Link>
                                <Link
                                    :href="`/jobs/${job.id}`"
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg text-white transition-opacity hover:opacity-90"
                                    :style="{ backgroundColor: colors.teal }"
                                >
                                    詳細
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="jobs.links && jobs.links.length > 3" class="px-6 py-4 border-t border-gray-100">
                        <nav class="flex justify-center gap-1">
                            <Link
                                v-for="link in jobs.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-2 text-sm rounded',
                                    link.active ? 'text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                :style="link.active ? { backgroundColor: colors.primary } : {}"
                                v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
