<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    draft: Object,
    duplicates: Object,
});

const confirmForm = useForm({});
const rejectForm = useForm({});

const hasDuplicates = computed(() => {
    return props.duplicates?.persons?.length > 0 || props.duplicates?.candidates?.length > 0;
});

const confirmDraft = () => {
    const message = hasDuplicates.value
        ? '重複の可能性がありますが、この候補者を登録しますか？'
        : 'この候補者を登録しますか？';

    if (confirm(message)) {
        confirmForm.post(`/intake/drafts/${props.draft.id}/confirm`);
    }
};

const rejectDraft = () => {
    if (confirm('この候補者を却下しますか？')) {
        rejectForm.post(`/intake/drafts/${props.draft.id}/reject`);
    }
};

const channelLabels = {
    direct: '直接応募',
    media: 'メディア経由',
    agent: 'エージェント推薦',
    referral: 'リファラル（社員紹介）',
};
</script>

<template>
    <Head title="候補者ドラフト確認" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link href="/intake/drafts" class="text-sm text-gray-500 hover:text-gray-700">
                        ← 確認待ちドラフト一覧
                    </Link>
                </div>

                <div class="card">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">
                                    {{ draft.name }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ channelLabels[draft.application_intake?.channel] }} からの取り込み
                                </p>
                            </div>
                            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                確認待ち
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 space-y-6">
                        <!-- 基本情報 -->
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">基本情報</h2>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">氏名</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ draft.name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">メールアドレス</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ draft.email || '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">電話番号</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ draft.phone || '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">受信日時</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ new Date(draft.application_intake?.received_at).toLocaleString('ja-JP') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- 重複警告 -->
                        <div
                            v-if="hasDuplicates"
                            class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg"
                        >
                            <h3 class="text-sm font-semibold text-yellow-800 mb-2">
                                ⚠️ 重複の可能性があります
                            </h3>

                            <!-- 既存の Person -->
                            <div v-if="duplicates.persons?.length > 0" class="mt-3">
                                <p class="text-sm text-yellow-700 mb-2">既存の人物情報:</p>
                                <ul class="space-y-2">
                                    <li
                                        v-for="item in duplicates.persons"
                                        :key="item.person.id"
                                        class="text-sm bg-white p-2 rounded border border-yellow-200"
                                    >
                                        <span class="font-medium">{{ item.person.name }}</span>
                                        <span class="text-gray-500 ml-2">({{ item.match_type }}が一致)</span>
                                        <br>
                                        <span class="text-xs text-gray-400">
                                            {{ item.person.email }} / {{ item.person.phone }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <!-- 既存の Candidate -->
                            <div v-if="duplicates.candidates?.length > 0" class="mt-3">
                                <p class="text-sm text-yellow-700 mb-2">この企業の候補者として既に登録済み:</p>
                                <ul class="space-y-2">
                                    <li
                                        v-for="item in duplicates.candidates"
                                        :key="item.candidate.id"
                                        class="text-sm bg-white p-2 rounded border border-yellow-200"
                                    >
                                        候補者ID: {{ item.candidate.id }}
                                        <span class="text-gray-500 ml-2">({{ item.match_type }}が一致)</span>
                                    </li>
                                </ul>
                            </div>

                            <p class="text-sm text-yellow-700 mt-3">
                                登録すると既存の情報に紐付けられます。
                            </p>
                        </div>

                        <!-- 抽出データ -->
                        <div v-if="draft.extracted_data && Object.keys(draft.extracted_data).length > 0">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">抽出データ</h2>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-lg p-4">
                                <div v-for="(value, key) in draft.extracted_data" :key="key">
                                    <dt class="text-sm font-medium text-gray-500">{{ key }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <template v-if="Array.isArray(value)">
                                            {{ value.join(', ') }}
                                        </template>
                                        <template v-else>
                                            {{ value }}
                                        </template>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- 生データ -->
                        <div v-if="draft.application_intake?.raw_data">
                            <details class="group">
                                <summary class="text-sm font-medium text-gray-500 cursor-pointer hover:text-gray-700">
                                    生データを表示
                                </summary>
                                <div class="mt-2 bg-gray-50 rounded-lg p-4">
                                    <pre class="text-xs text-gray-600 whitespace-pre-wrap overflow-auto max-h-64">{{ JSON.stringify(draft.application_intake.raw_data, null, 2) }}</pre>
                                </div>
                            </details>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                        <button
                            @click="rejectDraft"
                            :disabled="rejectForm.processing"
                            class="btn btn-secondary"
                        >
                            却下する
                        </button>
                        <button
                            @click="confirmDraft"
                            :disabled="confirmForm.processing"
                            class="btn btn-primary"
                        >
                            <span v-if="hasDuplicates">既存に紐付けて登録</span>
                            <span v-else>候補者として登録</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
