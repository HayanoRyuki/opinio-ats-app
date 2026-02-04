<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    applications: Object,
    stats: Object,
    jobs: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const jobId = ref(props.filters.job_id || '');

const statusLabels = {
    applied: '応募',
    in_progress: '選考中',
    offered: '内定',
    hired: '入社',
    rejected: '不採用',
    withdrawn: '辞退',
};

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

let searchTimeout = null;

const applyFilters = () => {
    router.get('/applications', {
        search: search.value || undefined,
        status: status.value || undefined,
        job_id: jobId.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch([status, jobId], applyFilters);
</script>

<template>
    <Head title="応募管理" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                        応募管理
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        すべての応募と選考状況を管理します
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: colors.primary + '15' }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">全応募</p>
                                <p class="text-xl font-bold" :style="{ color: colors.primary }">{{ stats.total }}</p>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">選考中</p>
                                <p class="text-xl font-bold" :style="{ color: colors.teal }">{{ stats.active }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-yellow-100">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">内定</p>
                                <p class="text-xl font-bold text-yellow-600">{{ stats.offered }}</p>
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
                                <p class="text-xs text-gray-500">入社</p>
                                <p class="text-xl font-bold" :style="{ color: colors.green }">{{ stats.hired }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow p-4 mb-6">
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="flex-1 min-w-[200px]">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="候補者名で検索..."
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-opacity-50"
                                :style="{ '--tw-ring-color': colors.teal }"
                            />
                        </div>
                        <select
                            v-model="status"
                            class="px-4 py-2 border border-gray-200 rounded-lg bg-white"
                            :style="{ color: colors.primary }"
                        >
                            <option value="">すべてのステータス</option>
                            <option v-for="(label, value) in statusLabels" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <select
                            v-model="jobId"
                            class="px-4 py-2 border border-gray-200 rounded-lg bg-white"
                            :style="{ color: colors.primary }"
                        >
                            <option value="">すべての求人</option>
                            <option v-for="job in jobs" :key="job.id" :value="job.id">
                                {{ job.title }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Applications List -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div v-if="applications.data.length === 0" class="p-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p>応募がありません</p>
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <div
                            v-for="application in applications.data"
                            :key="application.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center gap-4">
                                <!-- Avatar -->
                                <div
                                    class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center"
                                    :style="{ backgroundColor: colors.cream }"
                                >
                                    <svg
                                        class="w-6 h-6"
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
                                    <div class="flex items-center gap-2 mb-1">
                                        <Link
                                            :href="`/candidates/${application.candidate_id}`"
                                            class="font-semibold hover:underline"
                                            :style="{ color: colors.primary }"
                                        >
                                            {{ application.candidate?.name || '名前なし' }}
                                        </Link>
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                                            :class="{
                                                'bg-blue-100 text-blue-800': application.status === 'applied',
                                                'bg-yellow-100 text-yellow-800': application.status === 'offered',
                                                'bg-red-100 text-red-800': application.status === 'rejected',
                                                'bg-gray-100 text-gray-800': application.status === 'withdrawn',
                                            }"
                                            :style="application.status === 'in_progress' ? { backgroundColor: colors.teal + '20', color: colors.teal } : application.status === 'hired' ? { backgroundColor: colors.green + '20', color: colors.green } : {}"
                                        >
                                            {{ statusLabels[application.status] || application.status }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span v-if="application.job?.title">
                                            {{ application.job.title }}
                                        </span>
                                        <span v-if="application.current_step">
                                            ステップ: {{ application.current_step }}
                                        </span>
                                        <span>
                                            {{ new Date(application.created_at).toLocaleDateString('ja-JP') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action -->
                                <Link
                                    :href="`/applications/${application.id}`"
                                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                                    :style="{ backgroundColor: colors.teal + '15', color: colors.teal }"
                                >
                                    選考進捗 →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="applications.last_page > 1" class="px-6 py-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-500">
                                {{ applications.total }} 件中 {{ applications.from }} - {{ applications.to }} 件
                            </p>
                            <div class="flex gap-1">
                                <Link
                                    v-for="link in applications.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        'px-3 py-1 text-sm rounded',
                                        link.active ? 'text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                                        !link.url && 'opacity-50 cursor-not-allowed'
                                    ]"
                                    :style="link.active ? { backgroundColor: colors.primary } : {}"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
