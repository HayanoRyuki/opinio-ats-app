<script setup>
import { ref, nextTick, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    candidateId: Number,
    candidateName: String,
    messages: Array,
    externalChats: Array,
});

const body = ref('');
const sending = ref(false);
const chatContainer = ref(null);

// 外部チャット取り込みモーダル
const showImportModal = ref(false);
const importSource = ref('bizreach');
const importSourceLabel = ref('');
const importRawText = ref('');
const importing = ref(false);

// 折りたたみ管理
const expandedImports = ref({});

const sourceLabels = {
    bizreach: 'ビズリーチ',
    wantedly: 'Wantedly',
    other: 'その他',
};

const sourceBadgeColors = {
    bizreach: 'bg-blue-100 text-blue-700',
    wantedly: 'bg-pink-100 text-pink-700',
    other: 'bg-gray-100 text-gray-700',
};

// メッセージと外部チャットを時系列で統合
const timeline = computed(() => {
    const items = [];

    (props.messages || []).forEach(msg => {
        items.push({
            type: 'message',
            id: `msg-${msg.id}`,
            data: msg,
            date: new Date(msg.created_at),
        });
    });

    (props.externalChats || []).forEach(chat => {
        items.push({
            type: 'external',
            id: `ext-${chat.id}`,
            data: chat,
            date: new Date(chat.created_at),
        });
    });

    items.sort((a, b) => a.date - b.date);
    return items;
});

function scrollToBottom() {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    });
}

onMounted(() => {
    scrollToBottom();
});

function send() {
    if (!body.value.trim() || sending.value) return;

    sending.value = true;
    router.post(`/candidates/${props.candidateId}/messages`, {
        body: body.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            body.value = '';
            nextTick(() => scrollToBottom());
        },
        onFinish: () => {
            sending.value = false;
        },
    });
}

function openImportModal() {
    importSource.value = 'bizreach';
    importSourceLabel.value = '';
    importRawText.value = '';
    showImportModal.value = true;
}

function closeImportModal() {
    showImportModal.value = false;
}

function submitImport() {
    if (!importRawText.value.trim() || importing.value) return;

    importing.value = true;
    router.post(`/candidates/${props.candidateId}/external-chat`, {
        source: importSource.value,
        source_label: importSource.value === 'other' ? importSourceLabel.value : null,
        raw_text: importRawText.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showImportModal.value = false;
            importRawText.value = '';
            nextTick(() => scrollToBottom());
        },
        onFinish: () => {
            importing.value = false;
        },
    });
}

function toggleExpand(id) {
    expandedImports.value[id] = !expandedImports.value[id];
}

function formatTime(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleString('ja-JP', {
        month: 'numeric',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <div class="flex flex-col h-[500px]">
        <!-- ヘッダーバー -->
        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-100 bg-gray-50">
            <span class="text-xs text-gray-500">社内チャット &amp; 外部取り込み</span>
            <button
                @click="openImportModal"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md bg-[#332c54] text-white hover:bg-[#433b6e] transition-colors"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                外部チャット取り込み
            </button>
        </div>

        <!-- タイムライン -->
        <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-3">
            <div v-if="timeline.length === 0" class="text-center text-gray-400 py-12">
                まだメッセージはありません。<br>この候補者の状況を共有しましょう。
            </div>

            <template v-for="item in timeline" :key="item.id">
                <!-- 通常メッセージ -->
                <div v-if="item.type === 'message'" class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#4e878c] flex items-center justify-center text-white text-xs font-bold">
                        {{ item.data.sender_name?.charAt(0) || '?' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm font-medium text-gray-900">{{ item.data.sender_name || '不明' }}</span>
                            <span class="text-xs text-gray-400">{{ formatTime(item.data.created_at) }}</span>
                        </div>
                        <p class="text-sm text-gray-700 mt-0.5 whitespace-pre-wrap break-words">{{ item.data.body }}</p>
                    </div>
                </div>

                <!-- 外部チャット取り込みカード -->
                <div v-else-if="item.type === 'external'" class="mx-2 rounded-lg border border-indigo-100 bg-indigo-50/40 overflow-hidden">
                    <!-- カードヘッダー -->
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#332c54]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span :class="['inline-flex px-2 py-0.5 text-xs font-medium rounded-full', sourceBadgeColors[item.data.source] || sourceBadgeColors.other]">
                                {{ item.data.source_label || sourceLabels[item.data.source] || item.data.source }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ item.data.sender_name }} が取り込み
                            </span>
                        </div>
                        <span class="text-xs text-gray-400">{{ formatTime(item.data.created_at) }}</span>
                    </div>

                    <!-- AI要約 -->
                    <div class="px-4 pb-3">
                        <div class="bg-white rounded-md p-3 border border-indigo-100">
                            <div class="flex items-center gap-1.5 mb-2">
                                <svg class="w-3.5 h-3.5 text-[#65b891]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <span class="text-xs font-medium text-[#65b891]">OpinioAI 解析</span>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ item.data.summary || '要約を生成中...' }}</p>
                        </div>

                        <!-- 原文の折りたたみ -->
                        <button
                            @click="toggleExpand(item.id)"
                            class="mt-2 text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1"
                        >
                            <svg
                                :class="['w-3 h-3 transition-transform', expandedImports[item.id] ? 'rotate-90' : '']"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            原文を{{ expandedImports[item.id] ? '閉じる' : '表示' }}
                        </button>
                        <div v-if="expandedImports[item.id]" class="mt-2 bg-gray-50 rounded-md p-3 text-xs text-gray-600 whitespace-pre-wrap max-h-60 overflow-y-auto border border-gray-200">
                            {{ item.data.raw_text }}
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- 入力エリア -->
        <div class="border-t border-gray-200 p-3">
            <form @submit.prevent="send" class="flex gap-2">
                <textarea
                    v-model="body"
                    @keydown.enter.exact.prevent="send"
                    placeholder="メッセージを入力..."
                    rows="1"
                    class="flex-1 resize-none rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"
                />
                <button
                    type="submit"
                    :disabled="!body.trim() || sending"
                    class="px-4 py-2 bg-[#4e878c] text-white text-sm font-medium rounded-lg hover:bg-[#3d6b6f] disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                >
                    送信
                </button>
            </form>
        </div>

        <!-- 外部チャット取り込みモーダル -->
        <Teleport to="body">
            <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <!-- オーバーレイ -->
                <div class="absolute inset-0 bg-black/40" @click="closeImportModal"></div>

                <!-- モーダル本体 -->
                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-hidden">
                    <!-- ヘッダー -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">外部チャット取り込み</h3>
                            <button @click="closeImportModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- コンテンツ -->
                    <div class="px-6 py-4 overflow-y-auto max-h-[calc(90vh-140px)]">
                        <!-- 案内文 -->
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <p class="text-sm text-blue-800">
                                この候補者とのチャット履歴をここにそのままコピーペーストしてください。OpinioAIが自動で文章を解析し、現在の状況をダイジェストでまとめます。
                            </p>
                        </div>

                        <!-- ソース選択 -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">取り込み元サービス</label>
                            <select
                                v-model="importSource"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"
                            >
                                <option value="bizreach">ビズリーチ</option>
                                <option value="wantedly">Wantedly</option>
                                <option value="other">その他</option>
                            </select>
                        </div>

                        <!-- その他の場合のラベル -->
                        <div v-if="importSource === 'other'" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">サービス名</label>
                            <input
                                v-model="importSourceLabel"
                                type="text"
                                placeholder="例: Green, AMBI, doda..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"
                            />
                        </div>

                        <!-- テキストエリア -->
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">チャット履歴</label>
                            <textarea
                                v-model="importRawText"
                                rows="10"
                                placeholder="外部サービスのチャット画面からコピーした内容をここに貼り付けてください..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#4e878c] focus:border-transparent resize-y"
                            ></textarea>
                            <p class="text-xs text-gray-400 mt-1">最大 50,000 文字</p>
                        </div>
                    </div>

                    <!-- フッター -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-end gap-3">
                        <button
                            @click="closeImportModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            キャンセル
                        </button>
                        <button
                            @click="submitImport"
                            :disabled="!importRawText.trim() || importing"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#332c54] rounded-lg hover:bg-[#433b6e] disabled:opacity-40 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
                        >
                            <svg v-if="importing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ importing ? 'OpinioAI 解析中...' : '取り込み＆解析' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
