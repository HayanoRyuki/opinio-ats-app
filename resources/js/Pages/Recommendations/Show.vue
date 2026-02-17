<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    recommendation: Object,
    candidateSuggestions: Array,
    duplicateRecommendations: Array,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const linkForm = useForm({ candidate_id: null, application_id: null });
const unlinkForm = useForm({ candidate_id: null });
const archiveForm = useForm({});

const rec = computed(() => props.recommendation);
const candidateData = computed(() => rec.value.candidate_data || {});

const statusLabels = {
    received: '受信',
    processing: '処理中',
    pending: '確認待ち',
    draft: 'ドラフト',
    confirmed: '確定',
    rejected: '却下/アーカイブ',
    duplicate: '重複',
};

const agentDisplayName = computed(() => {
    if (rec.value.agent) {
        return `${rec.value.agent.organization} / ${rec.value.agent.name}`;
    }
    const parts = [rec.value.agent_company_name, rec.value.agent_name].filter(Boolean);
    return parts.join(' / ') || '不明';
});

const linkCandidate = (candidateId) => {
    linkForm.candidate_id = candidateId;
    linkForm.post(`/recommendations/${rec.value.id}/link`);
};

const unlinkCandidate = (candidateId) => {
    if (confirm('この紐付けを解除しますか？')) {
        unlinkForm.candidate_id = candidateId;
        unlinkForm.post(`/recommendations/${rec.value.id}/unlink`);
    }
};

const archive = () => {
    if (confirm('この推薦をアーカイブしますか？')) {
        archiveForm.post(`/recommendations/${rec.value.id}/archive`);
    }
};

const isProcessing = computed(() => linkForm.processing || unlinkForm.processing || archiveForm.processing);
</script>

<template>
    <Head title="推薦詳細" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Link href="/recommendations" class="text-sm hover:underline" :style="{ color: colors.teal }">
                        ← 推薦一覧
                    </Link>
                </div>

                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left Column -->
                    <div class="lg:w-80 flex-shrink-0">
                        <div class="lg:sticky lg:top-6 space-y-4">
                            <!-- Candidate Card -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <div class="px-6 py-5" :style="{ backgroundColor: colors.primary }">
                                    <h1 class="text-xl font-bold text-white">
                                        {{ candidateData.name || '名前なし' }}
                                    </h1>
                                    <p class="text-sm mt-1 opacity-80 text-white">
                                        エージェント推薦
                                    </p>
                                </div>

                                <!-- Badges -->
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                            :style="{ backgroundColor: colors.teal + '20', color: colors.teal }"
                                        >
                                            {{ statusLabels[rec.status] || rec.status }}
                                        </span>
                                        <span
                                            v-if="rec.links?.length > 0"
                                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                            :style="{ backgroundColor: colors.green + '20', color: colors.green }"
                                        >
                                            紐付済
                                        </span>
                                    </div>
                                </div>

                                <!-- Agent Info -->
                                <div class="px-6 py-4 border-b border-gray-100 space-y-2">
                                    <dt class="text-xs font-medium text-gray-400">エージェント</dt>
                                    <dd class="text-sm font-medium" :style="{ color: colors.primary }">
                                        {{ agentDisplayName }}
                                    </dd>
                                    <div v-if="rec.agent?.email || rec.agent_email" class="text-xs text-gray-500">
                                        {{ rec.agent?.email || rec.agent_email }}
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="px-6 py-5 space-y-3">
                                    <button
                                        v-if="rec.status !== 'rejected'"
                                        @click="archive"
                                        :disabled="isProcessing"
                                        class="w-full px-5 py-2.5 text-sm font-semibold rounded-lg border-2 border-red-300 text-red-600 bg-white hover:bg-red-50 transition-colors disabled:opacity-50"
                                    >
                                        アーカイブする
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="flex-1 space-y-6">
                        <!-- 注意書き: 推薦 ＝ 判断ではない -->
                        <div class="p-4 rounded-lg border" :style="{ backgroundColor: colors.cream, borderColor: colors.teal + '40' }">
                            <p class="text-sm" :style="{ color: colors.teal }">
                                以下はエージェントからの推薦情報です。推薦は判断の材料であり、判断そのものではありません。
                            </p>
                        </div>

                        <!-- 推薦コメント -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6 space-y-6">
                                <div v-if="rec.recommendation_comment">
                                    <h2 class="text-lg font-semibold mb-4 pb-2 border-b-2" :style="{ color: colors.primary, borderColor: colors.green }">
                                        推薦理由
                                    </h2>
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ rec.recommendation_comment }}</p>
                                </div>

                                <!-- 候補者基本情報 -->
                                <div>
                                    <h2 class="text-lg font-semibold mb-4 pb-2 border-b-2" :style="{ color: colors.primary, borderColor: colors.green }">
                                        候補者情報
                                    </h2>
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-if="candidateData.name">
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">氏名</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ candidateData.name }}</dd>
                                        </div>
                                        <div v-if="candidateData.email">
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">メール</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ candidateData.email }}</dd>
                                        </div>
                                        <div v-if="candidateData.phone">
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">電話</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ candidateData.phone }}</dd>
                                        </div>
                                        <div v-if="candidateData.resume_url">
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">履歴書</dt>
                                            <dd class="mt-1 text-sm">
                                                <a :href="candidateData.resume_url" target="_blank" class="underline" :style="{ color: colors.teal }">
                                                    ファイルを開く
                                                </a>
                                            </dd>
                                        </div>
                                        <div v-if="rec.job">
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">対象求人</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ rec.job.title }}</dd>
                                        </div>
                                        <div v-if="rec.received_at">
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">受信日時</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ new Date(rec.received_at).toLocaleString('ja-JP') }}</dd>
                                        </div>
                                    </dl>

                                    <!-- その他の candidate_data -->
                                    <div v-if="Object.keys(candidateData).filter(k => !['name','email','phone','resume_url'].includes(k)).length > 0" class="mt-4">
                                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 rounded-lg p-4" :style="{ backgroundColor: colors.cream }">
                                            <div v-for="(value, key) in candidateData" :key="key" v-show="!['name','email','phone','resume_url'].includes(key)">
                                                <dt class="text-sm font-medium" :style="{ color: colors.teal }">{{ key }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900">
                                                    <template v-if="Array.isArray(value)">{{ value.join(', ') }}</template>
                                                    <template v-else-if="typeof value === 'object'">{{ JSON.stringify(value) }}</template>
                                                    <template v-else>{{ value }}</template>
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 紐付け済み候補者 -->
                        <div v-if="rec.links?.length > 0" class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold mb-4 pb-2 border-b-2" :style="{ color: colors.primary, borderColor: colors.green }">
                                    紐付け済みの候補者
                                </h2>
                                <div class="space-y-3">
                                    <div v-for="link in rec.links" :key="link.id" class="flex items-center justify-between p-3 rounded-lg" :style="{ backgroundColor: colors.cream }">
                                        <div>
                                            <Link
                                                :href="`/candidates/${link.candidate.id}`"
                                                class="text-sm font-semibold hover:underline"
                                                :style="{ color: colors.primary }"
                                            >
                                                {{ link.candidate.person?.name || `候補者#${link.candidate.id}` }}
                                            </Link>
                                            <p class="text-xs text-gray-500">
                                                紐付日: {{ new Date(link.linked_at).toLocaleDateString('ja-JP') }}
                                            </p>
                                        </div>
                                        <button
                                            @click="unlinkCandidate(link.candidate.id)"
                                            :disabled="isProcessing"
                                            class="text-xs text-red-500 hover:underline disabled:opacity-50"
                                        >
                                            解除
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 紐付け候補 -->
                        <div v-if="candidateSuggestions?.length > 0" class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold mb-4 pb-2 border-b-2" :style="{ color: colors.primary, borderColor: colors.green }">
                                    紐付け候補
                                </h2>
                                <p class="text-sm text-gray-500 mb-4">既存の候補者と一致する可能性があります。</p>
                                <div class="space-y-3">
                                    <div v-for="suggestion in candidateSuggestions" :key="suggestion.candidate.id" class="flex items-center justify-between p-3 rounded-lg border border-gray-200">
                                        <div>
                                            <p class="text-sm font-semibold" :style="{ color: colors.primary }">
                                                {{ suggestion.candidate.person?.name || `候補者#${suggestion.candidate.id}` }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ suggestion.match_type }}が一致
                                                · {{ suggestion.candidate.person?.email }}
                                            </p>
                                        </div>
                                        <button
                                            @click="linkCandidate(suggestion.candidate.id)"
                                            :disabled="isProcessing"
                                            class="px-3 py-1.5 text-xs font-medium text-white rounded-lg disabled:opacity-50"
                                            :style="{ backgroundColor: colors.green }"
                                        >
                                            紐付ける
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 重複推薦 -->
                        <div v-if="duplicateRecommendations?.length > 0" class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold mb-4 pb-2 border-b-2 text-yellow-700 border-yellow-400">
                                    重複の可能性がある推薦
                                </h2>
                                <div class="space-y-2">
                                    <Link
                                        v-for="dup in duplicateRecommendations"
                                        :key="dup.recommendation.id"
                                        :href="`/recommendations/${dup.recommendation.id}`"
                                        class="block p-3 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors"
                                    >
                                        <p class="text-sm font-medium text-yellow-800">
                                            {{ dup.recommendation.candidate_data?.name || '名前なし' }}
                                            <span class="text-xs ml-2">({{ dup.match_type }}が一致)</span>
                                        </p>
                                        <p class="text-xs text-yellow-600">
                                            {{ dup.recommendation.agent_company_name }}
                                            · {{ dup.recommendation.received_at ? new Date(dup.recommendation.received_at).toLocaleDateString('ja-JP') : '' }}
                                        </p>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- 生データ -->
                        <div v-if="rec.application_intake?.raw_data" class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6">
                                <details class="group">
                                    <summary class="text-sm font-medium cursor-pointer hover:underline" :style="{ color: colors.teal }">
                                        原文・生データを表示
                                    </summary>
                                    <div class="mt-2 rounded-lg p-4" :style="{ backgroundColor: colors.cream }">
                                        <pre class="text-xs text-gray-600 whitespace-pre-wrap overflow-auto max-h-64">{{ JSON.stringify(rec.application_intake.raw_data, null, 2) }}</pre>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
