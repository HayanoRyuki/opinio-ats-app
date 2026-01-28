<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    user: Object,
    company: Object,
    membership: Object,
    authProfileUrl: String,
});

const roleLabels = {
    admin: '管理者',
    recruiter: '採用担当者',
    interviewer: '面接官',
    vendor: 'ベンダー',
    guest: 'ゲスト',
};
</script>

<template>
    <Head title="マイページ" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    マイページ
                </h1>

                <!-- Profile Card -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">
                            アカウント情報
                        </h2>
                        <a
                            :href="authProfileUrl"
                            target="_blank"
                            class="btn-primary inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            プロフィール編集
                        </a>
                    </div>

                    <div class="p-6">
                        <dl class="space-y-6">
                            <!-- User ID -->
                            <div class="flex items-center">
                                <dt class="w-32 text-sm font-medium text-gray-500">
                                    ユーザーID
                                </dt>
                                <dd class="text-sm text-gray-900">
                                    {{ user?.id || '-' }}
                                </dd>
                            </div>

                            <!-- Role -->
                            <div class="flex items-center">
                                <dt class="w-32 text-sm font-medium text-gray-500">
                                    権限
                                </dt>
                                <dd class="text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                        {{ roleLabels[user?.role] || user?.role || '-' }}
                                    </span>
                                </dd>
                            </div>

                            <!-- Company -->
                            <div class="flex items-center">
                                <dt class="w-32 text-sm font-medium text-gray-500">
                                    所属会社
                                </dt>
                                <dd class="text-sm text-gray-900">
                                    {{ company?.name || '-' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                名前・メールアドレス・パスワードの変更は、認証システム（Auth）で行います。
                                「プロフィール編集」ボタンから変更できます。
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.btn-primary {
    background-color: #4f46e5;
    color: white;
}
.btn-primary:hover {
    background-color: #4338ca;
}
</style>
