<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    agents: Object,
    filters: Object,
    stats: Object,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || 'active');

const applyFilter = () => {
    router.get('/agents', {
        search: search.value || undefined,
        status: status.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const typeLabels = { human: '人材紹介', ai: 'AI' };
</script>

<template>
    <Head title="エージェント管理" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                            エージェント管理
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ stats.active }}社が稼働中（全{{ stats.total }}社）
                        </p>
                    </div>
                    <Link
                        href="/agents/create"
                        class="px-4 py-2 text-sm font-semibold text-white rounded-lg shadow hover:shadow-lg transition-all"
                        :style="{ backgroundColor: colors.green }"
                    >
                        + エージェント新規登録
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <input
                            v-model="search"
                            @keyup.enter="applyFilter"
                            type="text"
                            placeholder="会社名・担当者名・メールで検索..."
                            class="flex-1 min-w-[200px] text-sm border border-gray-200 rounded-lg px-3 py-2"
                        />
                        <select
                            v-model="status"
                            @change="applyFilter"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2"
                        >
                            <option value="active">稼働中のみ</option>
                            <option value="inactive">停止中のみ</option>
                            <option value="all">すべて</option>
                        </select>
                        <button
                            @click="applyFilter"
                            class="px-4 py-2 text-sm font-medium text-white rounded-lg"
                            :style="{ backgroundColor: colors.teal }"
                        >
                            検索
                        </button>
                    </div>
                </div>

                <!-- Agent List -->
                <div class="space-y-3">
                    <div v-if="agents.data.length === 0" class="bg-white rounded-xl shadow p-12 text-center">
                        <p class="text-gray-500">該当するエージェントはありません</p>
                    </div>

                    <Link
                        v-for="agent in agents.data"
                        :key="agent.id"
                        :href="`/agents/${agent.id}`"
                        class="block bg-white rounded-xl shadow hover:shadow-lg transition-shadow p-4"
                    >
                        <div class="flex items-center gap-4">
                            <!-- Icon -->
                            <div
                                class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center"
                                :style="{ backgroundColor: agent.agent_type === 'ai' ? colors.teal + '20' : colors.cream }"
                            >
                                <svg v-if="agent.agent_type === 'human'" class="w-6 h-6" :style="{ color: colors.teal }" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                <svg v-else class="w-6 h-6" :style="{ color: colors.teal }" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                </svg>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold truncate" :style="{ color: colors.primary }">
                                        {{ agent.organization }}
                                    </h3>
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full flex-shrink-0"
                                        :style="{ backgroundColor: colors.teal + '20', color: colors.teal }"
                                    >
                                        {{ typeLabels[agent.agent_type] }}
                                    </span>
                                    <span
                                        v-if="!agent.is_active"
                                        class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-gray-200 text-gray-500"
                                    >
                                        停止中
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">
                                    {{ agent.name }}
                                    <span v-if="agent.email" class="ml-2">{{ agent.email }}</span>
                                </p>
                            </div>

                            <!-- Recommendation Count -->
                            <div class="text-right flex-shrink-0">
                                <p class="text-lg font-bold" :style="{ color: colors.primary }">
                                    {{ agent.recommendations_count || 0 }}
                                </p>
                                <p class="text-xs text-gray-400">推薦件数</p>
                            </div>

                            <!-- Arrow -->
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="agents.links && agents.links.length > 3" class="mt-6 flex justify-center">
                    <nav class="flex gap-1">
                        <Link
                            v-for="link in agents.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="['px-3 py-2 text-sm rounded', link.active ? 'text-white' : 'bg-white text-gray-700 hover:bg-gray-50', !link.url && 'opacity-50 cursor-not-allowed']"
                            :style="link.active ? { backgroundColor: colors.primary } : {}"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
