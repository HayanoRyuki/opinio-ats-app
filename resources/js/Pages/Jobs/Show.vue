<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    job: Object,
});

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
    active: 'bg-blue-100 text-blue-800',
    offered: 'bg-yellow-100 text-yellow-800',
    hired: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    withdrawn: 'bg-gray-100 text-gray-800',
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
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link href="/jobs" class="text-sm text-gray-500 hover:text-gray-700">
                        ← 求人一覧
                    </Link>
                </div>

                <!-- Header Card -->
                <div class="card mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">
                                    {{ job.title }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ employmentTypes[job.employment_type] || job.employment_type }}
                                    <span v-if="job.location"> • {{ job.location }}</span>
                                </p>
                            </div>
                            <span
                                :class="[
                                    'inline-flex px-3 py-1 text-sm font-medium rounded-full',
                                    statusColors[job.status] || 'bg-gray-100 text-gray-800'
                                ]"
                            >
                                {{ statusLabels[job.status] || job.status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- アクションボタン -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            <Link :href="`/jobs/${job.id}/edit`" class="btn btn-primary">
                                編集
                            </Link>

                            <Link :href="`/jobs/${job.id}/selection-steps`" class="btn btn-secondary">
                                選考ステップ設定
                            </Link>

                            <button
                                v-if="job.status === 'draft'"
                                @click="updateStatus('open')"
                                :disabled="processing"
                                class="btn btn-success"
                            >
                                公開する
                            </button>

                            <button
                                v-if="job.status === 'open'"
                                @click="updateStatus('paused')"
                                :disabled="processing"
                                class="btn btn-secondary"
                            >
                                一時停止
                            </button>

                            <button
                                v-if="job.status === 'paused'"
                                @click="updateStatus('open')"
                                :disabled="processing"
                                class="btn btn-success"
                            >
                                再開
                            </button>

                            <button
                                v-if="job.status !== 'closed' && job.status !== 'draft'"
                                @click="updateStatus('closed')"
                                :disabled="processing"
                                class="btn btn-danger"
                            >
                                募集終了
                            </button>
                        </div>

                        <!-- 基本情報 -->
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">雇用形態</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ employmentTypes[job.employment_type] || job.employment_type }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">勤務地</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ job.location || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">年収</dt>
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
                                <dt class="text-sm font-medium text-gray-500">応募数</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ job.applications?.length || 0 }} 件</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">作成日</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(job.created_at).toLocaleString('ja-JP') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">更新日</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(job.updated_at).toLocaleString('ja-JP') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- 仕事内容 -->
                <div v-if="job.description" class="card mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">仕事内容</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ job.description }}</p>
                    </div>
                </div>

                <!-- 応募資格 -->
                <div v-if="job.requirements" class="card mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">応募資格</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ job.requirements }}</p>
                    </div>
                </div>

                <!-- 福利厚生 -->
                <div v-if="job.benefits" class="card mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">福利厚生・待遇</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ job.benefits }}</p>
                    </div>
                </div>

                <!-- 応募者一覧 -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">応募者一覧</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div v-if="!job.applications || job.applications.length === 0" class="p-6 text-center text-gray-500">
                            応募者はまだいません
                        </div>
                        <div
                            v-for="application in job.applications"
                            :key="application.id"
                            class="p-6 hover:bg-gray-50"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <Link
                                        :href="`/candidates/${application.candidate_id}`"
                                        class="font-medium text-gray-900 hover:text-primary-600"
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
                                        :class="[
                                            'inline-flex px-3 py-1 text-sm font-medium rounded-full',
                                            applicationStatusColors[application.status] || 'bg-gray-100 text-gray-800'
                                        ]"
                                    >
                                        {{ applicationStatusLabels[application.status] || application.status }}
                                    </span>
                                    <Link
                                        :href="`/applications/${application.id}`"
                                        class="text-sm text-primary-600 hover:text-primary-800"
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
    </AppLayout>
</template>
