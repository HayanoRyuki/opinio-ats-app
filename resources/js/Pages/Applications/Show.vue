<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    application: Object,
    stepsProgress: Array,
});

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

const stepStatusColors = {
    not_started: 'bg-gray-200',
    pending: 'bg-gray-300',
    in_progress: 'bg-blue-500',
    passed: 'bg-green-500',
    failed: 'bg-red-500',
    skipped: 'bg-yellow-500',
};

const stepStatusLabels = {
    not_started: '未開始',
    pending: '未着手',
    in_progress: '進行中',
    passed: '通過',
    failed: '不通過',
    skipped: 'スキップ',
};

const processing = ref(false);
const showCompleteModal = ref(false);
const currentStepForComplete = ref(null);
const completeNotes = ref('');
const completeResult = ref('passed');

const startStep = (stepId) => {
    if (processing.value) return;
    processing.value = true;

    router.post(`/applications/${props.application.id}/steps/${stepId}/start`, {}, {
        onFinish: () => {
            processing.value = false;
        },
    });
};

const openCompleteModal = (step) => {
    currentStepForComplete.value = step;
    completeNotes.value = '';
    completeResult.value = 'passed';
    showCompleteModal.value = true;
};

const submitComplete = () => {
    if (processing.value || !currentStepForComplete.value) return;
    processing.value = true;

    router.post(`/applications/${props.application.id}/steps/${currentStepForComplete.value.selection_step.id}/complete`, {
        result: completeResult.value,
        notes: completeNotes.value,
    }, {
        onFinish: () => {
            processing.value = false;
            showCompleteModal.value = false;
            currentStepForComplete.value = null;
        },
    });
};

const updateStatus = (newStatus) => {
    if (processing.value) return;
    processing.value = true;

    router.patch(`/applications/${props.application.id}/status`, {
        status: newStatus,
    }, {
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`${application.candidate?.person?.name} - 選考進捗`" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link href="/applications" class="text-sm text-gray-500 hover:text-gray-700">
                        ← 応募一覧
                    </Link>
                </div>

                <!-- Header Card -->
                <div class="card mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">
                                    {{ application.candidate?.person?.name }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    <Link :href="`/jobs/${application.job_id}`" class="hover:text-primary-600">
                                        {{ application.job?.title }}
                                    </Link>
                                    への応募
                                </p>
                            </div>
                            <span
                                :class="[
                                    'inline-flex px-3 py-1 text-sm font-medium rounded-full',
                                    statusColors[application.status] || 'bg-gray-100 text-gray-800'
                                ]"
                            >
                                {{ statusLabels[application.status] || application.status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">応募日</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(application.applied_at).toLocaleDateString('ja-JP') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">現在のステップ</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ application.current_step || '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">候補者詳細</dt>
                                <dd class="mt-1">
                                    <Link
                                        :href="`/candidates/${application.candidate_id}`"
                                        class="text-sm text-primary-600 hover:text-primary-800"
                                    >
                                        プロフィールを見る →
                                    </Link>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ステータス変更</dt>
                                <dd class="mt-1">
                                    <select
                                        :value="application.status"
                                        @change="updateStatus($event.target.value)"
                                        class="input text-sm"
                                        :disabled="processing"
                                    >
                                        <option v-for="(label, value) in statusLabels" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- 選考ステップが未設定の場合 -->
                <div v-if="stepsProgress.length === 0" class="card p-6 text-center">
                    <p class="text-gray-500 mb-4">この求人には選考ステップが設定されていません</p>
                    <Link :href="`/jobs/${application.job_id}/selection-steps`" class="btn btn-primary">
                        選考ステップを設定
                    </Link>
                </div>

                <!-- 選考進捗 -->
                <div v-else class="card">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">選考進捗</h2>
                    </div>

                    <div class="p-6">
                        <!-- Progress Bar -->
                        <div class="flex items-center mb-8">
                            <div
                                v-for="(step, index) in stepsProgress"
                                :key="step.selection_step.id"
                                class="flex-1 flex items-center"
                            >
                                <div class="flex flex-col items-center flex-1">
                                    <div
                                        :class="[
                                            'w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium',
                                            stepStatusColors[step.status] || 'bg-gray-200'
                                        ]"
                                    >
                                        <span v-if="step.status === 'passed'">✓</span>
                                        <span v-else-if="step.status === 'failed'">✕</span>
                                        <span v-else>{{ index + 1 }}</span>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-600 text-center">
                                        {{ step.selection_step.name }}
                                    </p>
                                </div>
                                <div
                                    v-if="index < stepsProgress.length - 1"
                                    :class="[
                                        'h-1 flex-1',
                                        step.status === 'passed' ? 'bg-green-500' : 'bg-gray-200'
                                    ]"
                                />
                            </div>
                        </div>

                        <!-- Step Details -->
                        <div class="space-y-4">
                            <div
                                v-for="step in stepsProgress"
                                :key="step.selection_step.id"
                                :class="[
                                    'border rounded-lg p-4',
                                    step.status === 'in_progress' ? 'border-blue-300 bg-blue-50' : 'border-gray-200'
                                ]"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium text-gray-900">
                                            {{ step.selection_step.name }}
                                        </h3>
                                        <p v-if="step.selection_step.description" class="text-sm text-gray-500 mt-1">
                                            {{ step.selection_step.description }}
                                        </p>
                                        <div v-if="step.application_step" class="mt-2 text-sm text-gray-500">
                                            <span v-if="step.application_step.started_at">
                                                開始: {{ new Date(step.application_step.started_at).toLocaleDateString('ja-JP') }}
                                            </span>
                                            <span v-if="step.application_step.completed_at" class="ml-4">
                                                完了: {{ new Date(step.application_step.completed_at).toLocaleDateString('ja-JP') }}
                                            </span>
                                            <span v-if="step.application_step.scheduled_at" class="ml-4">
                                                予定: {{ new Date(step.application_step.scheduled_at).toLocaleString('ja-JP') }}
                                            </span>
                                        </div>
                                        <p v-if="step.application_step?.notes" class="mt-2 text-sm text-gray-600">
                                            メモ: {{ step.application_step.notes }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span
                                            :class="[
                                                'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                                                step.status === 'passed' ? 'bg-green-100 text-green-800' :
                                                step.status === 'failed' ? 'bg-red-100 text-red-800' :
                                                step.status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                                                'bg-gray-100 text-gray-800'
                                            ]"
                                        >
                                            {{ stepStatusLabels[step.status] }}
                                        </span>

                                        <!-- アクションボタン -->
                                        <button
                                            v-if="step.status === 'not_started' || step.status === 'pending'"
                                            @click="startStep(step.selection_step.id)"
                                            :disabled="processing"
                                            class="btn btn-primary text-sm"
                                        >
                                            開始
                                        </button>
                                        <button
                                            v-if="step.status === 'in_progress'"
                                            @click="openCompleteModal(step)"
                                            :disabled="processing"
                                            class="btn btn-success text-sm"
                                        >
                                            結果入力
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complete Modal -->
                <div
                    v-if="showCompleteModal"
                    class="fixed inset-0 z-50 overflow-y-auto"
                >
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-black opacity-30" @click="showCompleteModal = false" />
                        <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ currentStepForComplete?.selection_step?.name }} - 結果入力
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">結果</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center">
                                            <input
                                                type="radio"
                                                v-model="completeResult"
                                                value="passed"
                                                class="mr-2"
                                            />
                                            <span class="text-green-600 font-medium">通過</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input
                                                type="radio"
                                                v-model="completeResult"
                                                value="failed"
                                                class="mr-2"
                                            />
                                            <span class="text-red-600 font-medium">不通過</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        メモ（任意）
                                    </label>
                                    <textarea
                                        v-model="completeNotes"
                                        rows="3"
                                        class="input w-full"
                                        placeholder="評価コメントなど"
                                    />
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <button
                                    @click="showCompleteModal = false"
                                    class="btn btn-secondary"
                                >
                                    キャンセル
                                </button>
                                <button
                                    @click="submitComplete"
                                    :disabled="processing"
                                    class="btn btn-primary"
                                >
                                    {{ processing ? '保存中...' : '保存' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
