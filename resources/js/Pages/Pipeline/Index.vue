<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    pipeline: Array,
    jobs: Array,
    selectedJobId: [String, Number],
    stats: Object,
});

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

// 求人フィルタ変更
const changeJobFilter = (jobId) => {
    router.get('/pipeline', jobId ? { job_id: jobId } : {}, { preserveState: true });
};
</script>

<template>
    <Head title="パイプライン" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                            パイプライン
                        </h1>
                        <p class="text-sm text-gray-500">選考ステータス別の候補者一覧</p>
                    </div>

                    <!-- 求人フィルタ -->
                    <div class="mt-4 sm:mt-0 flex items-center gap-2">
                        <span class="text-sm text-gray-500">求人:</span>
                        <select
                            :value="selectedJobId || ''"
                            @change="changeJobFilter($event.target.value)"
                            class="rounded-lg border-gray-200 text-sm py-2 px-3 focus:ring-2 focus:border-transparent"
                            :style="{ '--tw-ring-color': colors.teal }"
                        >
                            <option value="">すべての求人</option>
                            <option v-for="job in jobs" :key="job.id" :value="job.id">
                                {{ job.title }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-xs text-gray-500">総応募数</p>
                        <p class="text-2xl font-bold" :style="{ color: colors.primary }">
                            {{ stats?.total || 0 }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-xs text-gray-500">選考中</p>
                        <p class="text-2xl font-bold" :style="{ color: colors.teal }">
                            {{ stats?.active || 0 }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-xs text-gray-500">入社</p>
                        <p class="text-2xl font-bold" :style="{ color: colors.green }">
                            {{ stats?.hired || 0 }}
                        </p>
                    </div>
                </div>

                <!-- Kanban Board -->
                <div class="overflow-x-auto pb-4">
                    <div class="flex gap-4" style="min-width: max-content;">
                        <!-- Each Stage Column -->
                        <div
                            v-for="stage in pipeline"
                            :key="stage.key"
                            class="w-72 flex-shrink-0"
                        >
                            <!-- Column Header -->
                            <div
                                class="rounded-t-xl px-4 py-3 flex items-center justify-between"
                                :style="{ backgroundColor: stage.color }"
                            >
                                <h3 class="font-semibold text-white">{{ stage.label }}</h3>
                                <span class="bg-white/20 text-white text-sm px-2 py-0.5 rounded-full">
                                    {{ stage.count }}
                                </span>
                            </div>

                            <!-- Column Content -->
                            <div
                                class="bg-white rounded-b-xl shadow-lg p-3 space-y-3"
                                style="min-height: 400px; max-height: 600px; overflow-y: auto;"
                            >
                                <!-- Application Card -->
                                <div
                                    v-for="app in stage.applications"
                                    :key="app.id"
                                    class="p-3 rounded-lg border border-gray-100 hover:shadow-md transition-shadow cursor-pointer"
                                    :style="{ backgroundColor: colors.cream }"
                                >
                                    <Link :href="`/applications/${app.id}`">
                                        <div class="flex items-start gap-3">
                                            <!-- Avatar -->
                                            <div
                                                class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                                :style="{ backgroundColor: 'white' }"
                                            >
                                                <svg class="w-5 h-5" :style="{ color: stage.color }" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                </svg>
                                            </div>

                                            <!-- Info -->
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-sm truncate" :style="{ color: colors.primary }">
                                                    {{ app.candidate?.name || '名前なし' }}
                                                </p>
                                                <p class="text-xs text-gray-500 truncate">
                                                    {{ app.job?.title || '求人なし' }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ new Date(app.updated_at).toLocaleDateString('ja-JP') }}
                                                </p>
                                            </div>
                                        </div>
                                    </Link>
                                </div>

                                <!-- Empty State -->
                                <div
                                    v-if="stage.applications.length === 0"
                                    class="text-center py-8"
                                >
                                    <p class="text-sm text-gray-400">該当なし</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Future Feature Notice -->
                <div class="mt-6 p-4 rounded-lg bg-white/50 border border-gray-200">
                    <p class="text-sm text-gray-500 text-center flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                        ドラッグ&ドロップでのステータス変更機能は今後追加予定です
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
