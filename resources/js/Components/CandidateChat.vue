<script setup>
import { ref, nextTick, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    candidateId: Number,
    messages: Array,
});

const body = ref('');
const sending = ref(false);
const chatContainer = ref(null);

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
        <!-- メッセージ一覧 -->
        <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-3">
            <div v-if="messages.length === 0" class="text-center text-gray-400 py-12">
                まだメッセージはありません。<br>この候補者の状況を共有しましょう。
            </div>
            <div
                v-for="msg in messages"
                :key="msg.id"
                class="flex items-start gap-3"
            >
                <!-- アバター -->
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#4e878c] flex items-center justify-center text-white text-xs font-bold">
                    {{ msg.user?.name?.charAt(0) || '?' }}
                </div>
                <!-- 本文 -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2">
                        <span class="text-sm font-medium text-gray-900">{{ msg.user?.name || '不明' }}</span>
                        <span class="text-xs text-gray-400">{{ formatTime(msg.created_at) }}</span>
                    </div>
                    <p class="text-sm text-gray-700 mt-0.5 whitespace-pre-wrap break-words">{{ msg.body }}</p>
                </div>
            </div>
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
    </div>
</template>
