<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const form = useForm({
    organization: '',
    name: '',
    email: '',
    phone: '',
    agent_type: 'human',
    notes: '',
    send_email: true,
});

const submit = () => {
    form.post('/agents');
};
</script>

<template>
    <Head title="エージェント新規登録" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Link href="/agents" class="text-sm hover:underline" :style="{ color: colors.teal }">
                        ← エージェント一覧
                    </Link>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5" :style="{ backgroundColor: colors.primary }">
                        <h1 class="text-xl font-bold text-white">エージェント新規登録</h1>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-5">
                        <!-- 種別 -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">種別</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="form.agent_type" value="human" class="accent-[#4e878c]" />
                                    <span class="text-sm">人材紹介</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="form.agent_type" value="ai" class="accent-[#4e878c]" />
                                    <span class="text-sm">AI</span>
                                </label>
                            </div>
                            <p v-if="form.errors.agent_type" class="text-red-500 text-xs mt-1">{{ form.errors.agent_type }}</p>
                        </div>

                        <!-- 会社名 -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">エージェント会社名 *</label>
                            <input
                                v-model="form.organization"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"
                                placeholder="例: リクルートエージェント"
                            />
                            <p v-if="form.errors.organization" class="text-red-500 text-xs mt-1">{{ form.errors.organization }}</p>
                        </div>

                        <!-- 担当者名 -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">担当者名 *</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"
                                placeholder="例: 田中 健太"
                            />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>

                        <!-- メール -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">メールアドレス *</label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"
                                placeholder="例: tanaka@example.com"
                            />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                            <p class="text-xs text-gray-400 mt-1">登録完了時にこのアドレスへ通知メールが送信されます</p>
                        </div>

                        <!-- 電話 -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">電話番号</label>
                            <input
                                v-model="form.phone"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"
                                placeholder="例: 03-1234-5678"
                            />
                            <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                        </div>

                        <!-- メモ -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">メモ</label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"
                                placeholder="得意領域、注意事項など"
                            ></textarea>
                            <p v-if="form.errors.notes" class="text-red-500 text-xs mt-1">{{ form.errors.notes }}</p>
                        </div>

                        <!-- メール送信確認 -->
                        <div v-if="form.agent_type === 'human'" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="form.send_email"
                                    class="mt-0.5 accent-[#4e878c]"
                                />
                                <div>
                                    <span class="text-sm font-medium text-gray-700">
                                        このエージェントに招待メールを送信する
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        推薦フォームへのリンクを含むウェルカムメールが送信されます
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3 pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2.5 text-sm font-bold text-white rounded-lg shadow hover:shadow-lg transition-all disabled:opacity-50"
                                :style="{ backgroundColor: colors.green }"
                            >
                                登録する
                            </button>
                            <Link
                                href="/agents"
                                class="px-6 py-2.5 text-sm font-semibold border-2 rounded-lg"
                                :style="{ borderColor: colors.teal, color: colors.teal }"
                            >
                                キャンセル
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
