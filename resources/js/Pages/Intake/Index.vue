<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    intakes: Object,
    stats: Object,
});

const channelLabels = {
    direct: '直接応募',
    scout: 'スカウト',
    agent: 'エージェント',
    referral: 'リファラル',
    // 旧値（後方互換）
    media: 'スカウト',
};

const statusLabels = {
    received: '受信済み',
    processing: '処理中',
    pending: '未処理',
    draft: 'ドラフト',
    confirmed: '確定済み',
    rejected: '却下',
    duplicate: '重複',
};

const statusColors = {
    received: 'bg-gray-100 text-gray-800',
    processing: 'bg-blue-50 text-blue-600',
    pending: 'bg-yellow-100 text-yellow-800',
    draft: 'bg-blue-100 text-blue-800',
    confirmed: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    duplicate: 'bg-gray-100 text-gray-800',
};
</script>

<template>
    <Head title="取り込み管理" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">取り込み管理</h1>
                    <Link
                        href="/intake/drafts"
                        class="btn btn-primary"
                    >
                        確認待ちドラフト ({{ stats.draft }})
                    </Link>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="card p-4">
                        <div class="text-sm text-gray-500">未処理</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</div>
                    </div>
                    <div class="card p-4">
                        <div class="text-sm text-gray-500">ドラフト</div>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.draft }}</div>
                    </div>
                    <div class="card p-4">
                        <div class="text-sm text-gray-500">確定済み</div>
                        <div class="text-2xl font-bold text-green-600">{{ stats.confirmed }}</div>
                    </div>
                </div>

                <!-- Table -->
                <div class="card overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    受信日時
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    チャネル
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    候補者名
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ステータス
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    操作
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="intakes.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    取り込みデータはありません
                                </td>
                            </tr>
                            <tr v-for="intake in intakes.data" :key="intake.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ new Date(intake.received_at).toLocaleString('ja-JP') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ channelLabels[intake.channel] || intake.channel }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ intake.draft?.name || intake.parsed_data?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                                            statusColors[intake.status]
                                        ]"
                                    >
                                        {{ statusLabels[intake.status] || intake.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Link
                                        v-if="intake.draft"
                                        :href="`/intake/drafts/${intake.draft.id}`"
                                        class="text-primary-600 hover:text-primary-900"
                                    >
                                        詳細
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
