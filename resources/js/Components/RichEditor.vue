<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const editor = ref(null);
const activeTab = ref('visual'); // 'visual' or 'html'
const htmlSource = ref(props.modelValue || '');

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
};

onMounted(() => {
    if (editor.value) {
        editor.value.innerHTML = props.modelValue || '';
    }
});

watch(() => props.modelValue, (val) => {
    if (activeTab.value === 'visual' && editor.value && editor.value.innerHTML !== val) {
        editor.value.innerHTML = val || '';
    }
    if (activeTab.value === 'html') {
        htmlSource.value = val || '';
    }
});

const onInput = () => {
    if (editor.value) {
        emit('update:modelValue', editor.value.innerHTML);
    }
};

const onHtmlInput = (e) => {
    htmlSource.value = e.target.value;
    emit('update:modelValue', e.target.value);
};

const switchTab = (tab) => {
    if (tab === 'html' && editor.value) {
        htmlSource.value = editor.value.innerHTML;
    }
    if (tab === 'visual' && editor.value) {
        editor.value.innerHTML = htmlSource.value;
    }
    activeTab.value = tab;
};

const execCommand = (command, value = null) => {
    document.execCommand(command, false, value);
    editor.value?.focus();
    onInput();
};

const insertHeading = (level) => {
    document.execCommand('formatBlock', false, `h${level}`);
    editor.value?.focus();
    onInput();
};

const insertLink = () => {
    const url = prompt('URLを入力してください:', 'https://');
    if (url) {
        document.execCommand('createLink', false, url);
        editor.value?.focus();
        onInput();
    }
};

const insertList = (type) => {
    if (type === 'ul') {
        document.execCommand('insertUnorderedList', false);
    } else {
        document.execCommand('insertOrderedList', false);
    }
    editor.value?.focus();
    onInput();
};

const toolbarButtons = [
    { label: 'B', command: 'bold', title: '太字' },
    { label: 'I', command: 'italic', title: '斜体', style: 'font-style: italic' },
    { label: 'U', command: 'underline', title: '下線', style: 'text-decoration: underline' },
    { type: 'divider' },
    { label: 'H2', action: () => insertHeading(2), title: '見出し2' },
    { label: 'H3', action: () => insertHeading(3), title: '見出し3' },
    { label: 'P', action: () => execCommand('formatBlock', 'p'), title: '段落' },
    { type: 'divider' },
    { label: '•', action: () => insertList('ul'), title: '箇条書き' },
    { label: '1.', action: () => insertList('ol'), title: '番号付きリスト' },
    { type: 'divider' },
    { label: '🔗', action: insertLink, title: 'リンク挿入' },
    { label: '—', command: 'insertHorizontalRule', title: '区切り線' },
];
</script>

<template>
    <div class="border border-gray-300 rounded-lg overflow-hidden">
        <!-- Toolbar -->
        <div class="flex items-center gap-0.5 px-3 py-2 bg-gray-50 border-b border-gray-300 flex-wrap">
            <template v-for="(btn, i) in toolbarButtons" :key="i">
                <div v-if="btn.type === 'divider'" class="w-px h-6 bg-gray-300 mx-1"></div>
                <button
                    v-else
                    type="button"
                    @click="btn.action ? btn.action() : execCommand(btn.command)"
                    class="px-2.5 py-1.5 text-sm font-medium rounded hover:bg-gray-200 transition-colors min-w-[32px]"
                    :style="btn.style || ''"
                    :title="btn.title"
                >
                    {{ btn.label }}
                </button>
            </template>

            <!-- Tab switcher -->
            <div class="ml-auto flex items-center gap-1 pl-4">
                <button
                    type="button"
                    @click="switchTab('visual')"
                    class="px-3 py-1 text-xs font-medium rounded transition-colors"
                    :class="activeTab === 'visual' ? 'text-white' : 'text-gray-500 hover:bg-gray-200'"
                    :style="activeTab === 'visual' ? { backgroundColor: colors.teal } : {}"
                >
                    ビジュアル
                </button>
                <button
                    type="button"
                    @click="switchTab('html')"
                    class="px-3 py-1 text-xs font-medium rounded transition-colors"
                    :class="activeTab === 'html' ? 'text-white' : 'text-gray-500 hover:bg-gray-200'"
                    :style="activeTab === 'html' ? { backgroundColor: colors.teal } : {}"
                >
                    HTML
                </button>
            </div>
        </div>

        <!-- Visual Editor -->
        <div
            v-show="activeTab === 'visual'"
            ref="editor"
            contenteditable="true"
            @input="onInput"
            class="rich-editor min-h-[400px] max-h-[600px] overflow-y-auto p-5 focus:outline-none text-gray-800 leading-relaxed"
            data-placeholder="ページの本文を入力してください..."
        ></div>

        <!-- HTML Editor -->
        <textarea
            v-show="activeTab === 'html'"
            :value="htmlSource"
            @input="onHtmlInput"
            class="w-full min-h-[400px] max-h-[600px] p-5 font-mono text-sm text-gray-800 focus:outline-none resize-y border-0"
            placeholder="<h2>見出し</h2><p>本文...</p>"
        ></textarea>
    </div>
</template>

<style scoped>
.rich-editor:empty::before {
    content: attr(data-placeholder);
    color: #9ca3af;
    pointer-events: none;
}

.rich-editor :deep(h2) {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 1.25rem 0 0.75rem;
    color: #332c54;
}

.rich-editor :deep(h3) {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 1rem 0 0.5rem;
    color: #332c54;
}

.rich-editor :deep(p) {
    margin: 0.5rem 0;
}

.rich-editor :deep(ul),
.rich-editor :deep(ol) {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
}

.rich-editor :deep(ul) {
    list-style-type: disc;
}

.rich-editor :deep(ol) {
    list-style-type: decimal;
}

.rich-editor :deep(li) {
    margin: 0.25rem 0;
}

.rich-editor :deep(a) {
    color: #4e878c;
    text-decoration: underline;
}

.rich-editor :deep(hr) {
    border: none;
    border-top: 2px solid #e5e7eb;
    margin: 1.5rem 0;
}

.rich-editor :deep(strong) {
    font-weight: 700;
}
</style>
