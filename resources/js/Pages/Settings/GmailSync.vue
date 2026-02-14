<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

defineProps({
    connections: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const flash = computed(() => ({
    success: page.props.flash?.success || null,
    error: page.props.flash?.error || null,
}));

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

function disconnectConnection(connectionId) {
    if (!confirm('この Gmail 接続を解除しますか？')) return;

    const form = useForm({});
    form.post(`/settings/gmail/${connectionId}/disconnect`);
}

function syncConnection(connectionId) {
    const form = useForm({});
    form.post(`/settings/gmail/${connectionId}/sync`);
}
</script>

<template>
    <Head title="Gmail連携設定" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                        Gmail連携設定
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Gmailアカウントを接続して、ビズリーチなどの通知メールを自動取得します。
                    </p>
                </div>

                <!-- Flash Messages -->
                <div
                    v-if="flash.success"
                    class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200"
                >
                    <p class="text-sm text-green-800">{{ flash.success }}</p>
                </div>
                <div
                    v-if="flash.error"
                    class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200"
                >
                    <p class="text-sm text-red-800">{{ flash.error }}</p>
                </div>

                <!-- Connect Button -->
                <div class="mb-8">
                    <a
                        href="/settings/gmail/connect"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-white text-sm font-medium shadow-sm hover:opacity-90 transition"
                        :style="{ backgroundColor: colors.teal }"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Gmailアカウントを接続
                    </a>
                </div>

                <!-- Connections List -->
                <div class="space-y-4">
                    <div v-if="connections.length === 0" class="bg-white rounded-lg shadow-sm border p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500 text-sm">
                            まだGmailアカウントが接続されていません。<br />
                            上のボタンからGmailを接続してください。
                        </p>
                    </div>

                    <div
                        v-for="conn in connections"
                        :key="conn.id"
                        class="bg-white rounded-lg shadow-sm border p-6"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-semibold text-lg" :style="{ color: colors.primary }">
                                        {{ conn.gmail_address }}
                                    </span>
                                    <span
                                        v-if="conn.is_active"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        接続中
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                    >
                                        無効
                                    </span>
                                </div>
                                <div class="mt-2 text-sm text-gray-500 space-y-1">
                                    <p>接続者: {{ conn.connected_by }}</p>
                                    <p>接続日: {{ conn.created_at }}</p>
                                    <p v-if="conn.last_sync_at">
                                        最終同期: {{ conn.last_sync_at }}
                                    </p>
                                    <p v-else class="text-amber-600">
                                        まだ同期されていません
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-2" v-if="conn.is_active">
                                <button
                                    @click="syncConnection(conn.id)"
                                    class="inline-flex items-center px-3 py-1.5 rounded text-xs font-medium text-white transition hover:opacity-90"
                                    :style="{ backgroundColor: colors.green }"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    今すぐ同期
                                </button>
                                <button
                                    @click="disconnectConnection(conn.id)"
                                    class="inline-flex items-center px-3 py-1.5 rounded text-xs font-medium text-red-700 bg-red-50 border border-red-200 hover:bg-red-100 transition"
                                >
                                    接続解除
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="font-semibold text-blue-900 mb-2">Gmail連携について</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>・Gmailに届く各媒体（ビズリーチ・Wantedly・doda・リクナビ・マイナビ）の通知メールを自動的に取得・解析します</li>
                        <li>・15分ごとに自動同期されます</li>
                        <li>・取得した候補者情報は「取り込み管理」のドラフトに追加されます</li>
                        <li>・Gmailの読み取り権限のみを使用し、メールの送信や削除は行いません</li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
