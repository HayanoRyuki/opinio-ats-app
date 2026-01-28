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
    draft: 'bg-gray-100 text-gray-800',
    open: 'bg-green-100 text-green-800',
    paused: 'bg-yellow-100 text-yellow-800',
    closed: 'bg-red-100 text-red-800',
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

watch(status, applyFilters);
</script>

<template>
    <Head title="求人管理" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">求人管理</h1>
                    <Link href="/jobs/create" class="btn btn-primary">
                        + 新規求人作成
                    </Link>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="status = ''; applyFilters()">
                        <div class="text-sm text-gray-500">全体</div>
                        <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
                    </div>
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="status = 'open'; applyFilters()">
                        <div class="text-sm text-gray-500">募集中</div>
                        <div class="text-2xl font-bold text-green-600">{{ stats.open }}</div>
                    </div>
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="status = 'draft'; applyFilters()">
                        <div class="text-sm text-gray-500">下書き</div>
                        <div class="text-2xl font-bold text-gray-600">{{ stats.draft }}</div>
                    </div>
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="status = 'closed'; applyFilters()">
                        <div class="text-sm text-gray-500">募集終了</div>
                        <div class="text-2xl font-bold text-red-600">{{ stats.closed }}</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card p-4 mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="求人タイトルで検索..."
                                class="input w-full"
                            />
                        </div>
                        <div>
                            <select v-model="status" class="input">
                                <option value="">全てのステータス</option>
                                <option value="draft">下書き</option>
                                <option value="open">募集中</option>
                                <option value="paused">一時停止</option>
                                <option value="closed">募集終了</option>
                            </select>
                        </div>
                        <button
                            v-if="search || status"
                            @click="clearFilters"
                            class="btn btn-secondary"
                        >
                            クリア
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="card overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    求人タイトル
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ステータス
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    応募数
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    作成日
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    操作
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="jobs.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    求人がありません
                                </td>
                            </tr>
                            <tr v-for="job in jobs.data" :key="job.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <Link :href="`/jobs/${job.id}`" class="font-medium text-gray-900 hover:text-primary-600">
                                        {{ job.title }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                                            statusColors[job.status] || 'bg-gray-100 text-gray-800'
                                        ]"
                                    >
                                        {{ statusLabels[job.status] || job.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ job.applications_count }} 件
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(job.created_at).toLocaleDateString('ja-JP') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                    <Link
                                        :href="`/jobs/${job.id}`"
                                        class="text-primary-600 hover:text-primary-900"
                                    >
                                        詳細
                                    </Link>
                                    <Link
                                        :href="`/jobs/${job.id}/edit`"
                                        class="text-gray-600 hover:text-gray-900"
                                    >
                                        編集
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="jobs.links && jobs.links.length > 3" class="mt-6 flex justify-center">
                    <nav class="flex gap-1">
                        <Link
                            v-for="link in jobs.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-2 text-sm rounded',
                                link.active
                                    ? 'bg-primary-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50',
                                !link.url && 'opacity-50 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
