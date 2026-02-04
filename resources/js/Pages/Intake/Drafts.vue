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

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};
</script>

<template>
    <Head title="確認待ちドラフト" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1
                            class="text-2xl font-bold"
                            :style="{ color: colors.primary }"
                        >
                            確認待ちドラフト
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            取り込まれた候補者情報を確認し、登録または却下してください。
                        </p>
                    </div>
                    <Link
                        href="/intake"
                        class="text-sm hover:underline"
                        :style="{ color: colors.teal }"
                    >
                        ← 取り込み一覧
                    </Link>
                </div>

                <div class="space-y-3">
                    <div v-if="drafts.data.length === 0" class="bg-white rounded-xl shadow p-12 text-center">
                        <div class="text-gray-400 mb-2">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">確認待ちの候補者はありません</p>
                    </div>

                    <Link
                        v-for="draft in drafts.data"
                        :key="draft.id"
                        :href="`/intake/drafts/${draft.id}`"
                        class="block bg-white rounded-xl shadow hover:shadow-lg transition-shadow p-4"
                    >
                        <div class="flex items-center gap-4">
                            <!-- Avatar -->
                            <div
                                class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center"
                                :style="{ backgroundColor: colors.cream }"
                            >
                                <svg
                                    class="w-6 h-6"
                                    :style="{ color: colors.teal }"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3
                                        class="font-semibold truncate"
                                        :style="{ color: colors.primary }"
                                    >
                                        {{ draft.name }}
                                    </h3>
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full flex-shrink-0"
                                        :style="{ backgroundColor: colors.teal + '20', color: colors.teal }"
                                    >
                                        {{ channelLabels[draft.application_intake?.channel] || draft.application_intake?.channel }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ draft.email || draft.phone || '-' }}
                                </p>
                            </div>

                            <!-- Arrow -->
                            <div class="flex-shrink-0">
                                <svg
                                    class="w-5 h-5 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>

                        <!-- 重複警告 -->
                        <div
                            v-if="draft.matched_person_id || draft.matched_candidate_id"
                            class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800"
                        >
                            ⚠️ 既存の候補者と重複している可能性があります
                        </div>
                    </Link>
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
                                    ? 'text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50',
                                !link.url && 'opacity-50 cursor-not-allowed'
                            ]"
                            :style="link.active ? { backgroundColor: colors.primary } : {}"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
