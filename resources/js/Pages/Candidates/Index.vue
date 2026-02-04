<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    candidates: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const channel = ref(props.filters.channel || '');

const channelLabels = {
    direct: '直接応募',
    scout: 'スカウト',
    agent: 'エージェント',
    referral: 'リファラル',
    // 旧値（後方互換）
    media: 'スカウト',
};

const channels = ['direct', 'scout', 'agent', 'referral'];

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const totalCount = computed(() => props.stats?.total || 0);

const applyFilters = () => {
    router.get('/candidates', {
        search: search.value || undefined,
        channel: channel.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const setChannel = (ch) => {
    channel.value = ch;
    applyFilters();
};

const clearFilters = () => {
    search.value = '';
    channel.value = '';
    router.get('/candidates');
};

// 検索入力のデバウンス
let searchTimeout;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});
</script>

<template>
    <Head title="候補者一覧" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                        候補者一覧
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        登録されているすべての候補者を管理します
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-5 gap-4 mb-8">
                    <button
                        @click="setChannel('')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': channel === '' }"
                        :style="channel === '' ? { '--tw-ring-color': colors.primary } : {}"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: colors.primary + '15' }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">全体</p>
                                <p class="text-xl font-bold" :style="{ color: colors.primary }">{{ stats.total }}</p>
                            </div>
                        </div>
                    </button>

                    <button
                        @click="setChannel('direct')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': channel === 'direct' }"
                        :style="channel === 'direct' ? { '--tw-ring-color': colors.teal } : {}"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: colors.teal + '20' }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.teal }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">直接応募</p>
                                <p class="text-xl font-bold" :style="{ color: colors.teal }">{{ stats.direct || 0 }}</p>
                            </div>
                        </div>
                    </button>

                    <button
                        @click="setChannel('scout')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': channel === 'scout' }"
                        :style="channel === 'scout' ? { '--tw-ring-color': colors.green } : {}"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ backgroundColor: colors.green + '20' }"
                            >
                                <svg class="w-5 h-5" :style="{ color: colors.green }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">スカウト</p>
                                <p class="text-xl font-bold" :style="{ color: colors.green }">{{ stats.scout || stats.media || 0 }}</p>
                            </div>
                        </div>
                    </button>

                    <button
                        @click="setChannel('agent')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': channel === 'agent' }"
                        style="--tw-ring-color: #9333ea"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-purple-100">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">エージェント</p>
                                <p class="text-xl font-bold text-purple-600">{{ stats.agent || 0 }}</p>
                            </div>
                        </div>
                    </button>

                    <button
                        @click="setChannel('referral')"
                        class="bg-white rounded-xl shadow p-5 text-left transition-all hover:shadow-lg"
                        :class="{ 'ring-2': channel === 'referral' }"
                        style="--tw-ring-color: #ea580c"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-orange-100">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">リファラル</p>
                                <p class="text-xl font-bold text-orange-600">{{ stats.referral || 0 }}</p>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Search -->
                <div class="bg-white rounded-xl shadow p-4 mb-6">
                    <div class="flex gap-4 items-center">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="名前、メール、電話番号で検索..."
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2"
                                :style="{ '--tw-ring-color': colors.teal }"
                            />
                        </div>
                        <button
                            v-if="search || channel"
                            @click="clearFilters"
                            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                            :style="{ color: colors.primary }"
                        >
                            クリア
                        </button>
                    </div>
                </div>

                <!-- Candidates List -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div v-if="candidates.data.length === 0" class="p-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p>候補者がいません</p>
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <Link
                            v-for="candidate in candidates.data"
                            :key="candidate.id"
                            :href="`/candidates/${candidate.id}`"
                            class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors"
                        >
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
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold" :style="{ color: colors.primary }">
                                        {{ candidate.name }}
                                    </span>
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                                        :class="{
                                            'bg-purple-100 text-purple-800': candidate.source_channel === 'agent',
                                            'bg-orange-100 text-orange-800': candidate.source_channel === 'referral',
                                        }"
                                        :style="candidate.source_channel === 'direct' ? { backgroundColor: colors.teal + '20', color: colors.teal } : (candidate.source_channel === 'scout' || candidate.source_channel === 'media') ? { backgroundColor: colors.green + '20', color: colors.green } : {}"
                                    >
                                        {{ channelLabels[candidate.source_channel] || candidate.source_channel }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span v-if="candidate.email">{{ candidate.email }}</span>
                                    <span v-if="candidate.phone">{{ candidate.phone }}</span>
                                    <span>応募: {{ candidate.applications?.length || 0 }}件</span>
                                </div>
                            </div>

                            <!-- Arrow -->
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="candidates.links && candidates.links.length > 3" class="px-6 py-4 border-t border-gray-100">
                        <nav class="flex justify-center gap-1">
                            <Link
                                v-for="link in candidates.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-2 text-sm rounded',
                                    link.active ? 'text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                :style="link.active ? { backgroundColor: colors.primary } : {}"
                                v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
