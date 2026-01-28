<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    job: Object,
    steps: Array,
});

// ステップデータをローカルで管理
const localSteps = ref(
    props.steps.length > 0
        ? props.steps.map(s => ({ ...s }))
        : []
);

const hasChanges = computed(() => {
    return JSON.stringify(localSteps.value) !== JSON.stringify(props.steps);
});

const addStep = () => {
    localSteps.value.push({
        id: null,
        name: '',
        description: '',
        duration_days: null,
        is_active: true,
    });
};

const removeStep = (index) => {
    localSteps.value.splice(index, 1);
};

const moveUp = (index) => {
    if (index === 0) return;
    const temp = localSteps.value[index];
    localSteps.value[index] = localSteps.value[index - 1];
    localSteps.value[index - 1] = temp;
};

const moveDown = (index) => {
    if (index === localSteps.value.length - 1) return;
    const temp = localSteps.value[index];
    localSteps.value[index] = localSteps.value[index + 1];
    localSteps.value[index + 1] = temp;
};

const saving = ref(false);

const saveSteps = () => {
    if (saving.value) return;
    saving.value = true;

    router.post(`/jobs/${props.job.id}/selection-steps`, {
        steps: localSteps.value,
    }, {
        onFinish: () => {
            saving.value = false;
        },
    });
};

const applyTemplate = () => {
    router.post(`/jobs/${props.job.id}/selection-steps/template`);
};
</script>

<template>
    <Head :title="`${job.title} - 選考ステップ設定`" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="`/jobs/${job.id}`" class="text-sm text-gray-500 hover:text-gray-700">
                        ← {{ job.title }} に戻る
                    </Link>
                </div>

                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">選考ステップ設定</h1>
                                <p class="text-sm text-gray-500 mt-1">{{ job.title }}</p>
                            </div>
                            <div class="flex gap-3">
                                <button
                                    v-if="localSteps.length === 0"
                                    @click="applyTemplate"
                                    class="btn btn-secondary"
                                >
                                    テンプレート適用
                                </button>
                                <button
                                    @click="saveSteps"
                                    :disabled="saving || !hasChanges"
                                    class="btn btn-primary"
                                >
                                    {{ saving ? '保存中...' : '保存' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- ステップが空の場合 -->
                        <div v-if="localSteps.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">選考ステップが未設定です</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                テンプレートを適用するか、手動でステップを追加してください。
                            </p>
                            <div class="mt-6 flex justify-center gap-3">
                                <button @click="applyTemplate" class="btn btn-secondary">
                                    テンプレート適用
                                </button>
                                <button @click="addStep" class="btn btn-primary">
                                    ステップを追加
                                </button>
                            </div>
                        </div>

                        <!-- ステップ一覧 -->
                        <div v-else class="space-y-4">
                            <div
                                v-for="(step, index) in localSteps"
                                :key="index"
                                class="border border-gray-200 rounded-lg p-4 bg-white"
                            >
                                <div class="flex items-start gap-4">
                                    <!-- 順序操作 -->
                                    <div class="flex flex-col gap-1 pt-2">
                                        <button
                                            @click="moveUp(index)"
                                            :disabled="index === 0"
                                            class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        </button>
                                        <span class="text-xs text-gray-500 text-center">{{ index + 1 }}</span>
                                        <button
                                            @click="moveDown(index)"
                                            :disabled="index === localSteps.length - 1"
                                            class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- フォーム -->
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                ステップ名 <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model="step.name"
                                                type="text"
                                                class="input w-full"
                                                placeholder="例: 一次面接"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                説明
                                            </label>
                                            <input
                                                v-model="step.description"
                                                type="text"
                                                class="input w-full"
                                                placeholder="例: 人事担当者による面接"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                所要日数
                                            </label>
                                            <input
                                                v-model="step.duration_days"
                                                type="number"
                                                min="1"
                                                class="input w-full"
                                                placeholder="7"
                                            />
                                        </div>
                                    </div>

                                    <!-- 削除ボタン -->
                                    <button
                                        @click="removeStep(index)"
                                        class="p-2 text-gray-400 hover:text-red-600"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- 追加ボタン -->
                            <button
                                @click="addStep"
                                class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 hover:text-gray-600 transition-colors"
                            >
                                + ステップを追加
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
