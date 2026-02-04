<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    drafts: Object,
});

const channelLabels = {
    direct: '直接応募',
    scout: 'スカウト',
    agent: 'エージェント',
    referral: 'リファラル',
    // 旧値（後方互換）
    media: 'スカウト',
};
</script>

<template>
    <Head title="確認待ちドラフト" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">確認待ちドラフト</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            取り込まれた候補者情報を確認し、登録または却下してください。
                        </p>
                    </div>
                    <Link href="/intake" class="btn btn-secondary">
                        ← 取り込み一覧
                    </Link>
                </div>

                <div class="space-y-4">
                    <div v-if="drafts.data.length === 0" class="card p-12 text-center">
                        <div class="text-gray-400 mb-2">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">確認待ちの候補者はありません</p>
                    </div>

                    <div
                        v-for="draft in drafts.data"
                        :key="draft.id"
                        class="card p-6 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ draft.name }}
                                    </h3>
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ channelLabels[draft.application_intake?.channel] || draft.application_intake?.channel }}
                                    </span>
                                </div>
                                <div class="mt-2 text-sm text-gray-500 space-y-1">
                                    <p v-if="draft.email">📧 {{ draft.email }}</p>
                                    <p v-if="draft.phone">📞 {{ draft.phone }}</p>
                                    <p class="text-xs text-gray-400">
                                        受信: {{ new Date(draft.application_intake?.received_at).toLocaleString('ja-JP') }}
                                    </p>
                                </div>

                                <!-- 重複警告 -->
                                <div
                                    v-if="draft.matched_person_id || draft.matched_candidate_id"
                                    class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg"
                                >
                                    <p class="text-sm text-yellow-800">
                                        ⚠️ 既存の候補者と重複している可能性があります
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-2 ml-4">
                                <Link
                                    :href="`/intake/drafts/${draft.id}`"
                                    class="btn btn-primary"
                                >
                                    確認する
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="drafts.links && drafts.links.length > 3" class="mt-6 flex justify-center">
                    <nav class="flex gap-1">
                        <Link
                            v-for="link in drafts.links"
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
