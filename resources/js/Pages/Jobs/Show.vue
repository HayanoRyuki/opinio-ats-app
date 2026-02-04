<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    job: Object,
});

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const statusLabels = {
    draft: '下書き',
    open: '募集中',
    paused: '一時停止',
    closed: '募集終了',
};

const statusColors = {
    draft: { bg: '#6b728020', color: '#6b7280' },
    open: { bg: colors.green + '20', color: colors.green },
    paused: { bg: '#f59e0b20', color: '#f59e0b' },
    closed: { bg: '#ef444420', color: '#ef4444' },
};

const employmentTypes = {
    full_time: '正社員',
    part_time: 'パート・アルバイト',
    contract: '契約社員',
    intern: 'インターン',
};

const applicationStatusLabels = {
    active: '選考中',
    offered: '内定',
    hired: '入社',
    rejected: '不採用',
    withdrawn: '辞退',
};

const applicationStatusColors = {
    active: { bg: colors.teal + '20', color: colors.teal },
    offered: { bg: '#f59e0b20', color: '#f59e0b' },
    hired: { bg: colors.green + '20', color: colors.green },
    rejected: { bg: '#ef444420', color: '#ef4444' },
    withdrawn: { bg: '#6b728020', color: '#6b7280' },
};

const processing = ref(false);

const updateStatus = (newStatus) => {
    if (processing.value) return;
    processing.value = true;

    router.patch(`/jobs/${props.job.id}/status`, {
        status: newStatus,
    }, {
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`${job.title} - 求人詳細`" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Back Link -->
                <div class="mb-4">
                    <Link
                        href="/jobs"
                        class="text-sm hover:underline"
                        :style="{ color: colors.teal }"
                    >
                        ← 求人一覧
                    </Link>
                </div>

                <!-- 2 Column Layout -->
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left Column: Status & Actions -->
                    <div class="lg:w-80 flex-shrink-0">
                        <div class="lg:sticky lg:top-6">
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <!-- Header with title -->
                                <div
                                    class="px-6 py-5"
                                    :style="{ backgroundColor: colors.primary }"
                                >
                                    <h1 class="text-xl font-bold text-white">
                                        {{ job.title }}
                                    </h1>
                                    <p class="text-sm mt-1 opacity-80 text-white">
                                        {{ employmentTypes[job.employment_type] || job.employment_type }}
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm" :style="{ color: colors.teal }">ステータス</span>
                                        <span
                                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                            :style="{
                                                backgroundColor: statusColors[job.status]?.bg || '#6b728020',
                                                color: statusColors[job.status]?.color || '#6b7280'
                                            }"
                                        >
                                            {{ statusLabels[job.status] || job.status }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Quick Stats -->
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold" :style="{ color: colors.primary }">
                                                {{ job.applications?.length || 0 }}
                                            </p>
                                            <p class="text-xs text-gray-500">応募数</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold" :style="{ color: colors.green }">
                                                {{ job.applications?.filter(a => a.status === 'hired').length || 0 }}
                                            </p>
                                            <p class="text-xs text-gray-500">入社</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="px-6 py-5 space-y-3">
                                    <Link
                                        :href="`/jobs/${job.id}/edit`"
                                        class="block w-full px-5 py-3 text-sm font-bold rounded-lg text-white text-center shadow-md hover:shadow-lg transition-all"
                                        :style="{ backgroundColor: colors.teal }"
                                    >
                                        編集
                                    </Link>

                                    <Link
                                        :href="`/jobs/${job.id}/selection-steps`"
                                        class="block w-full px-5 py-2.5 text-sm font-semibold rounded-lg border-2 text-center transition-colors"
                                        :style="{
                                            borderColor: colors.primary,
                                            color: colors.primary,
                                            backgroundColor: 'white'
                                        }"
                                    >
                                        選考ステップ設定
                                    </Link>

                                    <button
                                        v-if="job.status === 'draft'"
                                        @click="updateStatus('open')"
                                        :disabled="processing"
                                        class="w-full px-5 py-2.5 text-sm font-semibold rounded-lg text-white transition-all disabled:opacity-50"
                                        :style="{ backgroundColor: colors.green }"
                                    >
                                        公開する
                                    </button>

                                    <button
                                        v-if="job.status === 'open'"
                                        @click="updateStatus('paused')"
                                        :disabled="processing"
                                        class="w-full px-5 py-2.5 text-sm font-semibold rounded-lg border-2 border-yellow-400 text-yellow-600 bg-white hover:bg-yellow-50 transition-colors disabled:opacity-50"
                                    >
                                        一時停止
                                    </button>

                                    <button
                                        v-if="job.status === 'paused'"
                                        @click="updateStatus('open')"
                                        :disabled="processing"
                                        class="w-full px-5 py-2.5 text-sm font-semibold rounded-lg text-white transition-all disabled:opacity-50"
                                        :style="{ backgroundColor: colors.green }"
                                    >
                                        再開
                                    </button>

                                    <button
                                        v-if="job.status !== 'closed' && job.status !== 'draft'"
                                        @click="updateStatus('closed')"
                                        :disabled="processing"
                                        class="w-full px-5 py-2.5 text-sm font-semibold rounded-lg border-2 border-red-300 text-red-600 bg-white hover:bg-red-50 transition-colors disabled:opacity-50"
                                    >
                                        募集終了
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Details -->
                    <div class="flex-1 space-y-6">
                        <!-- 基本情報 -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <h2
                                    class="text-lg font-semibold mb-4 pb-2 border-b-2"
                                    :style="{ color: colors.primary, borderColor: colors.green }"
                                >
                                    基本情報
                                </h2>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium" :style="{ color: colors.teal }">雇用形態</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ employmentTypes[job.employment_type] || job.employment_type }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium" :style="{ color: colors.teal }">勤務地</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ job.location || '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium" :style="{ color: colors.teal }">年収</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <template v-if="job.salary_min && job.salary_max">
                                                {{ job.salary_min }}万円 〜 {{ job.salary_max }}万円
                                            </template>
                                            <template v-else-if="job.salary_min">
                                                {{ job.salary_min }}万円 〜
                                            </template>
                                            <template v-else-if="job.salary_max">
                                                〜 {{ job.salary_max }}万円
                                            </template>
                                            <template v-else>-</template>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium" :style="{ color: colors.teal }">応募数</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ job.applications?.length || 0 }} 件</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium" :style="{ color: colors.teal }">作成日</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ new Date(job.created_at).toLocaleString('ja-JP') }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium" :style="{ color: colors.teal }">更新日</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ new Date(job.updated_at).toLocaleString('ja-JP') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- 仕事内容 -->
                        <div v-if="job.description" class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <h2
                                    class="text-lg font-semibold mb-4 pb-2 border-b-2"
                                    :style="{ color: colors.primary, borderColor: colors.green }"
                                >
                                    仕事内容
                                </h2>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ job.description }}</p>
                            </div>
                        </div>

                        <!-- 応募資格 -->
                        <div v-if="job.requirements" class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <h2
                                    class="text-lg font-semibold mb-4 pb-2 border-b-2"
                                    :style="{ color: colors.primary, borderColor: colors.green }"
                                >
                                    応募資格
                                </h2>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ job.requirements }}</p>
                            </div>
                        </div>

                        <!-- 福利厚生 -->
                        <div v-if="job.benefits" class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <h2
                                    class="text-lg font-semibold mb-4 pb-2 border-b-2"
                                    :style="{ color: colors.primary, borderColor: colors.green }"
                                >
                                    福利厚生・待遇
                                </h2>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ job.benefits }}</p>
                            </div>
                        </div>

                        <!-- 応募者一覧 -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <h2
                                    class="text-lg font-semibold mb-4 pb-2 border-b-2"
                                    :style="{ color: colors.primary, borderColor: colors.green }"
                                >
                                    応募者一覧
                                </h2>

                                <div v-if="!job.applications || job.applications.length === 0" class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">応募者はまだいません</p>
                                </div>

                                <div v-else class="space-y-3">
                                    <div
                                        v-for="application in job.applications"
                                        :key="application.id"
                                        class="p-4 rounded-lg transition-colors hover:bg-gray-50"
                                        :style="{ backgroundColor: colors.cream }"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <Link
                                                    :href="`/candidates/${application.candidate_id}`"
                                                    class="font-medium hover:underline"
                                                    :style="{ color: colors.primary }"
                                                >
                                                    {{ application.candidate?.name || '名前なし' }}
                                                </Link>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    応募日: {{ new Date(application.created_at).toLocaleDateString('ja-JP') }}
                                                    <span v-if="application.current_step" class="ml-2">
                                                        • 現在: {{ application.current_step }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                                    :style="{
                                                        backgroundColor: applicationStatusColors[application.status]?.bg || '#6b728020',
                                                        color: applicationStatusColors[application.status]?.color || '#6b7280'
                                                    }"
                                                >
                                                    {{ applicationStatusLabels[application.status] || application.status }}
                                                </span>
                                                <Link
                                                    :href="`/applications/${application.id}`"
                                                    class="text-sm hover:underline"
                                                    :style="{ color: colors.teal }"
                                                >
                                                    選考進捗 →
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
