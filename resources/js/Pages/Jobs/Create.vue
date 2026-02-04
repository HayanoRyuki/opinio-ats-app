<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// Opinio Colors
const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const form = useForm({
    title: '',
    description: '',
    requirements: '',
    benefits: '',
    employment_type: 'full_time',
    location: '',
    salary_min: '',
    salary_max: '',
    status: 'draft',
});

const employmentTypes = {
    full_time: '正社員',
    part_time: 'パート・アルバイト',
    contract: '契約社員',
    intern: 'インターン',
};

const submit = () => {
    form.post('/jobs');
};
</script>

<template>
    <Head title="新規求人作成" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Back Link -->
                <div class="mb-4">
                    <Link
                        href="/jobs"
                        class="text-sm hover:underline"
                        :style="{ color: colors.teal }"
                    >
                        ← 求人一覧に戻る
                    </Link>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div
                        class="px-6 py-5"
                        :style="{ backgroundColor: colors.primary }"
                    >
                        <h1 class="text-xl font-bold text-white">新規求人作成</h1>
                        <p class="text-sm mt-1 opacity-80 text-white">新しい求人を作成します</p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- タイトル -->
                        <div>
                            <label for="title" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                求人タイトル <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                :style="{ '--tw-ring-color': colors.teal }"
                                placeholder="例: フロントエンドエンジニア"
                                required
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- 雇用形態 -->
                        <div>
                            <label for="employment_type" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                雇用形態 <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="employment_type"
                                v-model="form.employment_type"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                :style="{ '--tw-ring-color': colors.teal }"
                                required
                            >
                                <option v-for="(label, value) in employmentTypes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                            <p v-if="form.errors.employment_type" class="mt-1 text-sm text-red-600">
                                {{ form.errors.employment_type }}
                            </p>
                        </div>

                        <!-- 勤務地 -->
                        <div>
                            <label for="location" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                勤務地
                            </label>
                            <input
                                id="location"
                                v-model="form.location"
                                type="text"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                :style="{ '--tw-ring-color': colors.teal }"
                                placeholder="例: 東京都渋谷区"
                            />
                            <p v-if="form.errors.location" class="mt-1 text-sm text-red-600">
                                {{ form.errors.location }}
                            </p>
                        </div>

                        <!-- 給与 -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="salary_min" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                    最低年収（万円）
                                </label>
                                <input
                                    id="salary_min"
                                    v-model="form.salary_min"
                                    type="number"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                    :style="{ '--tw-ring-color': colors.teal }"
                                    placeholder="400"
                                />
                                <p v-if="form.errors.salary_min" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.salary_min }}
                                </p>
                            </div>
                            <div>
                                <label for="salary_max" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                    最高年収（万円）
                                </label>
                                <input
                                    id="salary_max"
                                    v-model="form.salary_max"
                                    type="number"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                    :style="{ '--tw-ring-color': colors.teal }"
                                    placeholder="800"
                                />
                                <p v-if="form.errors.salary_max" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.salary_max }}
                                </p>
                            </div>
                        </div>

                        <!-- 仕事内容 -->
                        <div>
                            <label for="description" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                仕事内容
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="6"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                :style="{ '--tw-ring-color': colors.teal }"
                                placeholder="具体的な仕事内容を記入してください"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- 応募資格 -->
                        <div>
                            <label for="requirements" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                応募資格
                            </label>
                            <textarea
                                id="requirements"
                                v-model="form.requirements"
                                rows="4"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                :style="{ '--tw-ring-color': colors.teal }"
                                placeholder="必須スキル、歓迎スキルなど"
                            />
                            <p v-if="form.errors.requirements" class="mt-1 text-sm text-red-600">
                                {{ form.errors.requirements }}
                            </p>
                        </div>

                        <!-- 福利厚生 -->
                        <div>
                            <label for="benefits" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                福利厚生・待遇
                            </label>
                            <textarea
                                id="benefits"
                                v-model="form.benefits"
                                rows="4"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                :style="{ '--tw-ring-color': colors.teal }"
                                placeholder="社会保険、休日、その他福利厚生など"
                            />
                            <p v-if="form.errors.benefits" class="mt-1 text-sm text-red-600">
                                {{ form.errors.benefits }}
                            </p>
                        </div>

                        <!-- ステータス -->
                        <div>
                            <label for="status" class="block text-sm font-medium mb-2" :style="{ color: colors.teal }">
                                公開ステータス
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:border-transparent transition-all"
                                :style="{ '--tw-ring-color': colors.teal }"
                            >
                                <option value="draft">下書き</option>
                                <option value="open">募集中</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                            <Link
                                href="/jobs"
                                class="px-6 py-3 text-sm font-semibold rounded-lg border-2 transition-colors"
                                :style="{
                                    borderColor: colors.primary,
                                    color: colors.primary,
                                    backgroundColor: 'white'
                                }"
                            >
                                キャンセル
                            </Link>
                            <button
                                type="submit"
                                class="px-6 py-3 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-all disabled:opacity-50"
                                :style="{ backgroundColor: colors.green }"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? '作成中...' : '求人を作成' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
