<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RichEditor from '@/Components/RichEditor.vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    job: Object,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const form = useForm({
    title: '',
    slug: '',
    content: '',
    featured_image: null,
    status: 'draft',
});

// 画像プレビュー
const imagePreview = ref(null);
const fileInput = ref(null);

const onImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.featured_image = file;
        const reader = new FileReader();
        reader.onload = (ev) => {
            imagePreview.value = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.featured_image = null;
    imagePreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

// タイトルからスラッグを自動生成
const autoSlug = ref(true);
watch(() => form.title, (val) => {
    if (autoSlug.value && val) {
        form.slug = val
            .toLowerCase()
            .replace(/[^a-z0-9\u3040-\u309f\u30a0-\u30ff\u4e00-\u9fff\s\-]/g, '')
            .replace(/[\s\u3040-\u309f\u30a0-\u30ff\u4e00-\u9fff]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '')
            || 'page-' + Date.now();
    }
});

const onSlugInput = () => {
    autoSlug.value = false;
};

const submit = () => {
    form.post(`/jobs/${props.job.id}/pages`, {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`求人ページ作成 - ${job.title}`" />

    <AppLayout>
        <div class="py-6" :style="{ backgroundColor: colors.cream, minHeight: '100vh' }">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Back Link -->
                <div class="mb-4">
                    <Link
                        :href="`/jobs/${job.id}`"
                        class="text-sm hover:underline"
                        :style="{ color: colors.teal }"
                    >
                        ← {{ job.title }}
                    </Link>
                </div>

                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                        求人ページ作成
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        「{{ job.title }}」の公開ページを作成します
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Title & Slug -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 space-y-5">
                            <h2
                                class="text-lg font-semibold pb-2 border-b-2"
                                :style="{ color: colors.primary, borderColor: colors.green }"
                            >
                                基本設定
                            </h2>

                            <div>
                                <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">
                                    ページタイトル <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all"
                                    :style="{ '--tw-ring-color': colors.teal }"
                                    placeholder="例: フロントエンドエンジニア募集"
                                />
                                <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">
                                    URL スラッグ <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500 whitespace-nowrap">/careers/</span>
                                    <input
                                        v-model="form.slug"
                                        @input="onSlugInput"
                                        type="text"
                                        class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all"
                                        placeholder="frontend-engineer"
                                    />
                                </div>
                                <p class="text-xs text-gray-400 mt-1">半角英数字とハイフンのみ使用可能</p>
                                <p v-if="form.errors.slug" class="text-red-500 text-sm mt-1">{{ form.errors.slug }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">
                                    ステータス
                                </label>
                                <select
                                    v-model="form.status"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent"
                                >
                                    <option value="draft">下書き</option>
                                    <option value="published">公開</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h2
                                class="text-lg font-semibold pb-2 border-b-2 mb-4"
                                :style="{ color: colors.primary, borderColor: colors.green }"
                            >
                                アイキャッチ画像
                            </h2>

                            <div v-if="!imagePreview" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-gray-400 transition-colors">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-4">
                                    <label class="cursor-pointer">
                                        <span
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white transition-colors"
                                            :style="{ backgroundColor: colors.teal }"
                                        >
                                            画像を選択
                                        </span>
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            accept="image/*"
                                            class="hidden"
                                            @change="onImageChange"
                                        />
                                    </label>
                                </div>
                                <p class="text-xs text-gray-400 mt-2">PNG, JPG, WEBP（最大5MB）</p>
                            </div>

                            <div v-else class="relative">
                                <img :src="imagePreview" class="w-full h-64 object-cover rounded-xl" />
                                <button
                                    type="button"
                                    @click="removeImage"
                                    class="absolute top-3 right-3 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <p v-if="form.errors.featured_image" class="text-red-500 text-sm mt-2">{{ form.errors.featured_image }}</p>
                        </div>
                    </div>

                    <!-- Content Editor -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h2
                                class="text-lg font-semibold pb-2 border-b-2 mb-4"
                                :style="{ color: colors.primary, borderColor: colors.green }"
                            >
                                ページ本文
                            </h2>

                            <RichEditor v-model="form.content" />
                            <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</p>
                        </div>
                    </div>

                    <!-- Preview hint -->
                    <div
                        v-if="form.slug"
                        class="bg-white rounded-xl shadow-lg p-4 flex items-center gap-3"
                    >
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">公開URL</p>
                            <p class="text-sm font-mono" :style="{ color: colors.teal }">
                                /careers/{{ form.slug }}
                            </p>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-all disabled:opacity-50"
                            :style="{ backgroundColor: colors.teal }"
                        >
                            {{ form.processing ? '保存中...' : '保存する' }}
                        </button>
                        <Link
                            :href="`/jobs/${job.id}`"
                            class="px-6 py-3 text-sm font-medium rounded-lg border-2 transition-colors"
                            :style="{ borderColor: colors.primary, color: colors.primary }"
                        >
                            キャンセル
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
