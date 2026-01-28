<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    job: Object,
});

const form = useForm({
    title: props.job.title || '',
    description: props.job.description || '',
    requirements: props.job.requirements || '',
    benefits: props.job.benefits || '',
    employment_type: props.job.employment_type || 'full_time',
    location: props.job.location || '',
    salary_min: props.job.salary_min || '',
    salary_max: props.job.salary_max || '',
    status: props.job.status || 'draft',
});

const employmentTypes = {
    full_time: '正社員',
    part_time: 'パート・アルバイト',
    contract: '契約社員',
    intern: 'インターン',
};

const statusLabels = {
    draft: '下書き',
    open: '募集中',
    paused: '一時停止',
    closed: '募集終了',
};

const submit = () => {
    form.put(`/jobs/${props.job.id}`);
};
</script>

<template>
    <Head :title="`${job.title} - 編集`" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="`/jobs/${job.id}`" class="text-sm text-gray-500 hover:text-gray-700">
                        ← 求人詳細に戻る
                    </Link>
                </div>

                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h1 class="text-xl font-bold text-gray-900">求人を編集</h1>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- タイトル -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                求人タイトル <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="input w-full"
                                placeholder="例: フロントエンドエンジニア"
                                required
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- 雇用形態 -->
                        <div>
                            <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1">
                                雇用形態 <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="employment_type"
                                v-model="form.employment_type"
                                class="input w-full"
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
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                勤務地
                            </label>
                            <input
                                id="location"
                                v-model="form.location"
                                type="text"
                                class="input w-full"
                                placeholder="例: 東京都渋谷区"
                            />
                            <p v-if="form.errors.location" class="mt-1 text-sm text-red-600">
                                {{ form.errors.location }}
                            </p>
                        </div>

                        <!-- 給与 -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="salary_min" class="block text-sm font-medium text-gray-700 mb-1">
                                    最低年収（万円）
                                </label>
                                <input
                                    id="salary_min"
                                    v-model="form.salary_min"
                                    type="number"
                                    class="input w-full"
                                    placeholder="400"
                                />
                                <p v-if="form.errors.salary_min" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.salary_min }}
                                </p>
                            </div>
                            <div>
                                <label for="salary_max" class="block text-sm font-medium text-gray-700 mb-1">
                                    最高年収（万円）
                                </label>
                                <input
                                    id="salary_max"
                                    v-model="form.salary_max"
                                    type="number"
                                    class="input w-full"
                                    placeholder="800"
                                />
                                <p v-if="form.errors.salary_max" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.salary_max }}
                                </p>
                            </div>
                        </div>

                        <!-- 仕事内容 -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                仕事内容
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="6"
                                class="input w-full"
                                placeholder="具体的な仕事内容を記入してください"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- 応募資格 -->
                        <div>
                            <label for="requirements" class="block text-sm font-medium text-gray-700 mb-1">
                                応募資格
                            </label>
                            <textarea
                                id="requirements"
                                v-model="form.requirements"
                                rows="4"
                                class="input w-full"
                                placeholder="必須スキル、歓迎スキルなど"
                            />
                            <p v-if="form.errors.requirements" class="mt-1 text-sm text-red-600">
                                {{ form.errors.requirements }}
                            </p>
                        </div>

                        <!-- 福利厚生 -->
                        <div>
                            <label for="benefits" class="block text-sm font-medium text-gray-700 mb-1">
                                福利厚生・待遇
                            </label>
                            <textarea
                                id="benefits"
                                v-model="form.benefits"
                                rows="4"
                                class="input w-full"
                                placeholder="社会保険、休日、その他福利厚生など"
                            />
                            <p v-if="form.errors.benefits" class="mt-1 text-sm text-red-600">
                                {{ form.errors.benefits }}
                            </p>
                        </div>

                        <!-- ステータス -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                公開ステータス
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="input w-full"
                            >
                                <option v-for="(label, value) in statusLabels" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <Link :href="`/jobs/${job.id}`" class="btn btn-secondary">
                                キャンセル
                            </Link>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? '保存中...' : '変更を保存' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
