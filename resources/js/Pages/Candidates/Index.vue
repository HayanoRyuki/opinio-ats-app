<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    candidates: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const channel = ref(props.filters.channel || '');

const channelLabels = {
    direct: '直接応募',
    media: 'メディア',
    agent: 'エージェント',
    referral: 'リファラル',
};

const applyFilters = () => {
    router.get('/candidates', {
        search: search.value || undefined,
        channel: channel.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
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

watch(channel, applyFilters);
</script>

<template>
    <Head title="候補者一覧" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">候補者一覧</h1>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="channel = ''; applyFilters()">
                        <div class="text-sm text-gray-500">全体</div>
                        <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
                    </div>
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="channel = 'direct'; applyFilters()">
                        <div class="text-sm text-gray-500">直接応募</div>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.direct }}</div>
                    </div>
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="channel = 'media'; applyFilters()">
                        <div class="text-sm text-gray-500">メディア</div>
                        <div class="text-2xl font-bold text-green-600">{{ stats.media }}</div>
                    </div>
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="channel = 'agent'; applyFilters()">
                        <div class="text-sm text-gray-500">エージェント</div>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.agent }}</div>
                    </div>
                    <div class="card p-4 cursor-pointer hover:shadow-md" @click="channel = 'referral'; applyFilters()">
                        <div class="text-sm text-gray-500">リファラル</div>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.referral }}</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card p-4 mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="名前、メール、電話番号で検索..."
                                class="input w-full"
                            />
                        </div>
                        <div>
                            <select v-model="channel" class="input">
                                <option value="">全てのチャネル</option>
                                <option value="direct">直接応募</option>
                                <option value="media">メディア</option>
                                <option value="agent">エージェント</option>
                                <option value="referral">リファラル</option>
                            </select>
                        </div>
                        <button
                            v-if="search || channel"
                            @click="clearFilters"
                            class="btn btn-secondary"
                        >
                            クリア
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="card overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    候補者
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    連絡先
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    チャネル
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    応募
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    登録日
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    操作
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="candidates.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    候補者がいません
                                </td>
                            </tr>
                            <tr v-for="candidate in candidates.data" :key="candidate.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        {{ candidate.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div v-if="candidate.email">{{ candidate.email }}</div>
                                    <div v-if="candidate.phone">{{ candidate.phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                                            {
                                                'bg-blue-100 text-blue-800': candidate.source_channel === 'direct',
                                                'bg-green-100 text-green-800': candidate.source_channel === 'media',
                                                'bg-purple-100 text-purple-800': candidate.source_channel === 'agent',
                                                'bg-orange-100 text-orange-800': candidate.source_channel === 'referral',
                                            }
                                        ]"
                                    >
                                        {{ channelLabels[candidate.source_channel] || candidate.source_channel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ candidate.applications?.length || 0 }} 件
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(candidate.created_at).toLocaleDateString('ja-JP') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Link
                                        :href="`/candidates/${candidate.id}`"
                                        class="text-primary-600 hover:text-primary-900"
                                    >
                                        詳細
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="candidates.links && candidates.links.length > 3" class="mt-6 flex justify-center">
                    <nav class="flex gap-1">
                        <Link
                            v-for="link in candidates.links"
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
