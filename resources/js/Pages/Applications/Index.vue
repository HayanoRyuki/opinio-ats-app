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
    active: '選考中',
    offered: '内定',
    hired: '入社',
    rejected: '不採用',
    withdrawn: '辞退',
};

const statusColors = {
    active: 'bg-blue-100 text-blue-800',
    offered: 'bg-yellow-100 text-yellow-800',
    hired: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    withdrawn: 'bg-gray-100 text-gray-800',
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
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">応募管理</h1>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="card p-4">
                        <p class="text-sm text-gray-500">全応募</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-sm text-gray-500">選考中</p>
                        <p class="text-2xl font-bold text-blue-600">{{ stats.active }}</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-sm text-gray-500">内定</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ stats.offered }}</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-sm text-gray-500">入社</p>
                        <p class="text-2xl font-bold text-green-600">{{ stats.hired }}</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-6">
                    <div class="p-4 flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="候補者名で検索..."
                                class="input w-full"
                            />
                        </div>
                        <div>
                            <select v-model="status" class="input">
                                <option value="">すべてのステータス</option>
                                <option v-for="(label, value) in statusLabels" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <select v-model="jobId" class="input">
                                <option value="">すべての求人</option>
                                <option v-for="job in jobs" :key="job.id" :value="job.id">
                                    {{ job.title }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="card overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    候補者
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    求人
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    現在のステップ
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ステータス
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    応募日
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="applications.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    応募がありません
                                </td>
                            </tr>
                            <tr
                                v-for="application in applications.data"
                                :key="application.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link
                                        :href="`/candidates/${application.candidate_id}`"
                                        class="font-medium text-gray-900 hover:text-primary-600"
                                    >
                                        {{ application.candidate?.person?.name || '名前なし' }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link
                                        :href="`/jobs/${application.job_id}`"
                                        class="text-sm text-gray-600 hover:text-primary-600"
                                    >
                                        {{ application.job?.title }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ application.current_step || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                                            statusColors[application.status] || 'bg-gray-100 text-gray-800'
                                        ]"
                                    >
                                        {{ statusLabels[application.status] || application.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(application.applied_at).toLocaleDateString('ja-JP') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <Link
                                        :href="`/applications/${application.id}`"
                                        class="text-primary-600 hover:text-primary-800 text-sm font-medium"
                                    >
                                        選考進捗
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="applications.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-500">
                                {{ applications.total }} 件中 {{ applications.from }} - {{ applications.to }} 件
                            </p>
                            <div class="flex gap-2">
                                <Link
                                    v-for="link in applications.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-1 text-sm rounded',
                                        link.active
                                            ? 'bg-primary-600 text-white'
                                            : link.url
                                                ? 'bg-white text-gray-700 border hover:bg-gray-50'
                                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]"
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
