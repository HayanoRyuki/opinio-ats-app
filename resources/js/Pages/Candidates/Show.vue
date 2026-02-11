<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CandidateChat from '@/Components/CandidateChat.vue';

const props = defineProps({
    candidate: Object,
    canViewChat: Boolean,
    messages: Array,
});

const activeTab = ref('info');

const channelLabels = {
    direct: '直接応募',
    media: 'メディア経由',
    agent: 'エージェント推薦',
    referral: 'リファラル（社員紹介）',
};

const statusLabels = {
    active: '選考中',
    offered: '内定',
    hired: '入社',
    rejected: '不採用',
    withdrawn: '辞退',
};

const statusColors = {
    active: 'bg-blue-100 text-blue-800',
    offered: 'bg-yellow-100 text-yellow-800',
    hired: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    withdrawn: 'bg-gray-100 text-gray-800',
};
</script>

<template>
    <Head :title="`${candidate.name} - 候補者詳細`" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link href="/candidates" class="text-sm text-gray-500 hover:text-gray-700">
                        ← 候補者一覧
                    </Link>
                </div>

                <!-- Header Card -->
                <div class="card mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">
                                    {{ candidate.name }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ channelLabels[candidate.source_channel] }} から登録
                                </p>
                            </div>
                            <span
                                :class="[
                                    'inline-flex px-3 py-1 text-sm font-medium rounded-full',
                                    {
                                        'bg-blue-100 text-blue-800': candidate.source_channel === 'direct',
                                        'bg-green-100 text-green-800': candidate.source_channel === 'media',
                                        'bg-purple-100 text-purple-800': candidate.source_channel === 'agent',
                                        'bg-orange-100 text-orange-800': candidate.source_channel === 'referral',
                                    }
                                ]"
                            >
                                {{ channelLabels[candidate.source_channel] }}
                            </span>
                        </div>
                    </div>

                    <!-- タブ -->
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button
                                @click="activeTab = 'info'"
                                :class="[
                                    'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                                    activeTab === 'info'
                                        ? 'border-[#4e878c] text-[#4e878c]'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                基本情報
                            </button>
                            <button
                                v-if="canViewChat"
                                @click="activeTab = 'chat'"
                                :class="[
                                    'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                                    activeTab === 'chat'
                                        ? 'border-[#4e878c] text-[#4e878c]'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                チャット
                                <span v-if="messages && messages.length > 0" class="ml-1 inline-flex items-center justify-center w-5 h-5 text-xs font-medium bg-[#4e878c] text-white rounded-full">
                                    {{ messages.length }}
                                </span>
                            </button>
                        </nav>
                    </div>

                    <!-- 基本情報タブ -->
                    <div v-show="activeTab === 'info'" class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">基本情報</h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">氏名</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ candidate.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ふりがな</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ candidate.name_kana || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">メールアドレス</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a v-if="candidate.email" :href="`mailto:${candidate.email}`" class="text-primary-600 hover:underline">
                                        {{ candidate.email }}
                                    </a>
                                    <span v-else>-</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">電話番号</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a v-if="candidate.phone" :href="`tel:${candidate.phone}`" class="text-primary-600 hover:underline">
                                        {{ candidate.phone }}
                                    </a>
                                    <span v-else>-</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">登録日</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(candidate.created_at).toLocaleString('ja-JP') }}
                                </dd>
                            </div>
                        </dl>

                        <!-- Profile data -->
                        <div v-if="candidate.profile && Object.keys(candidate.profile).length > 0" class="mt-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-3">追加情報</h3>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-lg p-4">
                                <div v-for="(value, key) in candidate.profile" :key="key">
                                    <dt class="text-sm font-medium text-gray-500">{{ key }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <template v-if="Array.isArray(value)">
                                            {{ value.join(', ') }}
                                        </template>
                                        <template v-else>
                                            {{ value }}
                                        </template>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- チャットタブ -->
                    <div v-if="canViewChat" v-show="activeTab === 'chat'">
                        <CandidateChat
                            :candidate-id="candidate.id"
                            :messages="messages || []"
                        />
                    </div>
                </div>

                <!-- Applications -->
                <div class="card mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">応募履歴</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div v-if="!candidate.applications || candidate.applications.length === 0" class="p-6 text-center text-gray-500">
                            応募履歴はありません
                        </div>
                        <div
                            v-for="application in candidate.applications"
                            :key="application.id"
                            class="p-6 hover:bg-gray-50"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900">
                                        {{ application.job?.title || '求人情報なし' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        応募日: {{ new Date(application.created_at).toLocaleDateString('ja-JP') }}
                                    </p>
                                </div>
                                <span
                                    :class="[
                                        'inline-flex px-3 py-1 text-sm font-medium rounded-full',
                                        statusColors[application.status] || 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ statusLabels[application.status] || application.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div v-if="candidate.notes" class="card mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">メモ</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ candidate.notes }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
