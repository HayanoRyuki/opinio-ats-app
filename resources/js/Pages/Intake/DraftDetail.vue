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
const promoteForm = useForm({});

// フォーム処理中かどうか
const isProcessing = computed(() => {
    return confirmForm.processing || rejectForm.processing || promoteForm.processing;
});

const hasDuplicates = computed(() => {
    return props.duplicates?.persons?.length > 0 || props.duplicates?.candidates?.length > 0;
});

// 仮応募かどうか
const isPreliminary = computed(() => {
    return props.draft?.is_preliminary ?? false;
});

// 仮応募から昇格済みかどうか
const isPromoted = computed(() => {
    return props.draft?.is_preliminary && props.draft?.promoted_at;
});

const confirmDraft = () => {
    // 仮応募で未昇格の場合は、昇格して登録するか確認
    if (isPreliminary.value && !isPromoted.value) {
        const message = hasDuplicates.value
            ? 'これは仮応募（スカウト反応）です。正式応募に昇格して候補者登録しますか？\n\n※重複の可能性があります'
            : 'これは仮応募（スカウト反応）です。正式応募に昇格して候補者登録しますか？';

        if (confirm(message)) {
            confirmForm.post(`/intake/drafts/${props.draft.id}/confirm-and-promote`);
        }
        return;
    }

    const message = hasDuplicates.value
        ? '重複の可能性がありますが、この候補者を登録しますか？'
        : 'この候補者を登録しますか？';

    if (confirm(message)) {
        confirmForm.post(`/intake/drafts/${props.draft.id}/confirm`);
    }
};

// 仮応募から正式応募に昇格（候補者登録はしない）
const promoteDraft = () => {
    if (confirm('この仮応募を正式応募に昇格しますか？\n（候補者登録は別途行います）')) {
        promoteForm.post(`/intake/drafts/${props.draft.id}/promote`);
    }
};

const rejectDraft = () => {
    if (confirm('この候補者を却下しますか？')) {
        rejectForm.post(`/intake/drafts/${props.draft.id}/reject`);
    }
};

const channelLabels = {
    direct: '直接応募',
    scout: 'スカウト',
    agent: 'エージェント',
    referral: 'リファラル',
    // 旧値（後方互換）
    media: 'スカウト',
};

// 写真URL（draft直下 or extracted_data内）
const photoUrl = computed(() => {
    return props.draft?.photo_url
        || props.draft?.extracted_data?.photo_url
        || props.draft?.extracted_data?.photo
        || props.draft?.extracted_data?.image_url
        || props.draft?.extracted_data?.avatar_url
        || null;
});

// Opinio Colors
const colors = {
    primary: '#332c54',    // Dark purple
    teal: '#4e878c',       // Teal
    green: '#65b891',      // Green
    cream: '#f4f4ed',      // Light cream
};
</script>

<template>
    <Head title="候補者ドラフト確認" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Link
                        href="/intake/drafts"
                        class="text-sm hover:underline"
                        :style="{ color: colors.teal }"
                    >
                        ← 確認待ちドラフト一覧
                    </Link>
                </div>

                <!-- 2 Column Layout -->
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left Column: Name, Channel, Badge, Buttons -->
                    <div class="lg:w-80 flex-shrink-0">
                        <div class="lg:sticky lg:top-6">
                            <div
                                class="rounded-xl shadow-lg overflow-hidden"
                                :style="{ backgroundColor: 'white' }"
                            >
                                <!-- Header with name -->
                                <div
                                    class="px-6 py-5"
                                    :style="{ backgroundColor: colors.primary }"
                                >
                                    <h1 class="text-xl font-bold text-white">
                                        {{ draft.name }}
                                    </h1>
                                    <p class="text-sm mt-1 opacity-80 text-white">
                                        {{ channelLabels[draft.application_intake?.channel] }} からの取り込み
                                    </p>
                                </div>

                                <!-- Avatar -->
                                <div class="px-6 py-5 flex justify-center border-b border-gray-100">
                                    <div
                                        class="w-24 h-24 rounded-full overflow-hidden flex items-center justify-center"
                                        :style="{ backgroundColor: photoUrl ? 'transparent' : colors.cream }"
                                    >
                                        <!-- 写真がある場合 -->
                                        <img
                                            v-if="photoUrl"
                                            :src="photoUrl"
                                            :alt="draft.name"
                                            class="w-full h-full object-cover"
                                        />
                                        <!-- 写真がない場合：Lucide User Icon -->
                                        <svg
                                            v-else
                                            class="w-12 h-12"
                                            :style="{ color: colors.teal }"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Badges -->
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <div class="flex flex-wrap gap-2">
                                        <!-- 仮応募バッジ -->
                                        <span
                                            v-if="isPreliminary && !isPromoted"
                                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                            :style="{ backgroundColor: colors.teal + '20', color: colors.teal }"
                                        >
                                            仮応募
                                        </span>
                                        <span
                                            v-else-if="isPromoted"
                                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                            :style="{ backgroundColor: colors.green + '20', color: colors.green }"
                                        >
                                            昇格済み
                                        </span>
                                        <span
                                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                                            :style="{ backgroundColor: colors.primary + '15', color: colors.primary }"
                                        >
                                            確認待ち
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="px-6 py-5 space-y-3">
                                    <!-- 登録ボタン（一番目立つ） -->
                                    <button
                                        @click="confirmDraft"
                                        :disabled="isProcessing"
                                        class="w-full px-5 py-3 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-all disabled:opacity-50"
                                        :style="{ backgroundColor: colors.green }"
                                    >
                                        <template v-if="isPreliminary && !isPromoted">
                                            ✓ 昇格して候補者登録
                                        </template>
                                        <template v-else-if="hasDuplicates">
                                            ✓ 既存に紐付けて登録
                                        </template>
                                        <template v-else>
                                            ✓ 候補者として登録
                                        </template>
                                    </button>

                                    <!-- 仮応募の場合は昇格のみのボタンも表示 -->
                                    <button
                                        v-if="isPreliminary && !isPromoted"
                                        @click="promoteDraft"
                                        :disabled="isProcessing"
                                        class="w-full px-5 py-2.5 text-sm font-semibold rounded-lg border-2 transition-colors disabled:opacity-50"
                                        :style="{
                                            borderColor: colors.teal,
                                            color: colors.teal,
                                            backgroundColor: 'white'
                                        }"
                                    >
                                        ↑ 正式応募に昇格のみ
                                    </button>

                                    <!-- 却下ボタン -->
                                    <button
                                        @click="rejectDraft"
                                        :disabled="isProcessing"
                                        class="w-full px-5 py-2.5 text-sm font-semibold rounded-lg border-2 border-red-300 text-red-600 bg-white hover:bg-red-50 transition-colors disabled:opacity-50"
                                    >
                                        ✕ 却下する
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Details -->
                    <div class="flex-1">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <!-- Content -->
                            <div class="p-6 space-y-6">
                                <!-- 基本情報 -->
                                <div>
                                    <h2
                                        class="text-lg font-semibold mb-4 pb-2 border-b-2"
                                        :style="{ color: colors.primary, borderColor: colors.green }"
                                    >
                                        基本情報
                                    </h2>
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">氏名</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ draft.name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">メールアドレス</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ draft.email || '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">電話番号</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ draft.phone || '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">受信日時</dt>
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
                                    <h2
                                        class="text-lg font-semibold mb-4 pb-2 border-b-2"
                                        :style="{ color: colors.primary, borderColor: colors.green }"
                                    >
                                        抽出データ
                                    </h2>
                                    <dl
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4 rounded-lg p-4"
                                        :style="{ backgroundColor: colors.cream }"
                                    >
                                        <div v-for="(value, key) in draft.extracted_data" :key="key">
                                            <dt class="text-sm font-medium" :style="{ color: colors.teal }">{{ key }}</dt>
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
                                        <summary
                                            class="text-sm font-medium cursor-pointer hover:underline"
                                            :style="{ color: colors.teal }"
                                        >
                                            生データを表示
                                        </summary>
                                        <div
                                            class="mt-2 rounded-lg p-4"
                                            :style="{ backgroundColor: colors.cream }"
                                        >
                                            <pre class="text-xs text-gray-600 whitespace-pre-wrap overflow-auto max-h-64">{{ JSON.stringify(draft.application_intake.raw_data, null, 2) }}</pre>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
