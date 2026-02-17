<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ agent: Object });

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const form = useForm({
    organization: props.agent.organization,
    name: props.agent.name,
    email: props.agent.email || '',
    phone: props.agent.phone || '',
    agent_type: props.agent.agent_type,
    notes: props.agent.notes || '',
    is_active: props.agent.is_active,
});

const submit = () => {
    form.put(`/agents/${props.agent.id}`);
};
</script>

<template>
    <Head title="エージェント編集" />

    <AppLayout>
        <div class="py-8" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Link :href="`/agents/${agent.id}`" class="text-sm hover:underline" :style="{ color: colors.teal }">
                        ← {{ agent.organization }} に戻る
                    </Link>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5" :style="{ backgroundColor: colors.primary }">
                        <h1 class="text-xl font-bold text-white">エージェント編集</h1>
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
                        </div>

                        <!-- 会社名 -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">エージェント会社名 *</label>
                            <input v-model="form.organization" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent" />
                            <p v-if="form.errors.organization" class="text-red-500 text-xs mt-1">{{ form.errors.organization }}</p>
                        </div>

                        <!-- 担当者名 -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">担当者名 *</label>
                            <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent" />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>

                        <!-- メール -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">メールアドレス</label>
                            <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent" />
                        </div>

                        <!-- 電話 -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">電話番号</label>
                            <input v-model="form.phone" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent" />
                        </div>

                        <!-- メモ -->
                        <div>
                            <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">メモ</label>
                            <textarea v-model="form.notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4e878c] focus:border-transparent"></textarea>
                        </div>

                        <!-- 有効/無効 -->
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" v-model="form.is_active" class="accent-[#65b891] w-4 h-4" />
                                <span class="text-sm font-medium" :style="{ color: colors.primary }">稼働中</span>
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
                                更新する
                            </button>
                            <Link
                                :href="`/agents/${agent.id}`"
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
