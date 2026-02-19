<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RichEditor from '@/Components/RichEditor.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    job: Object,
    page: Object,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const form = useForm({
    _method: 'PUT',
    title: props.page.title,
    slug: props.page.slug,
    content: props.page.content || '',
    featured_image: null,
    remove_featured_image: false,
    status: props.page.status,
});

// 画像プレビュー
const imagePreview = ref(null);
const fileInput = ref(null);
const existingImage = computed(() => {
    if (form.remove_featured_image) return null;
    return props.page.featured_image ? `/storage/${props.page.featured_image}` : null;
});

const onImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.featured_image = file;
        form.remove_featured_image = false;
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
    form.remove_featured_image = true;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const removeNewImage = () => {
    form.featured_image = null;
    imagePreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const submit = () => {
    form.post(`/jobs/${props.job.id}/pages/${props.page.id}`, {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`ページ編集 - ${page.title}`" />

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
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold" :style="{ color: colors.primary }">
                            ページ編集
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            「{{ page.title }}」を編集します
                        </p>
                    </div>
                    <span
                        class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                        :style="{
                            backgroundColor: page.status === 'published' ? colors.green + '20' : '#6b728020',
                            color: page.status === 'published' ? colors.green : '#6b7280'
                        }"
                    >
                        {{ page.status === 'published' ? '公開中' : '下書き' }}
                    </span>
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
                                        type="text"
                                        class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all"
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

                            <!-- 新しい画像が選択されている場合 -->
                            <div v-if="imagePreview" class="relative">
                                <img :src="imagePreview" class="w-full h-64 object-cover rounded-xl" />
                                <div class="absolute top-3 right-3 flex gap-2">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">新しい画像</span>
                                    <button
                                        type="button"
                                        @click="removeNewImage"
                                        class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- 既存の画像がある場合 -->
                            <div v-else-if="existingImage" class="relative">
                                <img :src="existingImage" class="w-full h-64 object-cover rounded-xl" />
                                <div class="absolute top-3 right-3 flex gap-2">
                                    <label class="cursor-pointer bg-white text-gray-700 text-xs px-3 py-1.5 rounded-full shadow-lg hover:bg-gray-50 transition-colors font-medium">
                                        変更
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            accept="image/*"
                                            class="hidden"
                                            @change="onImageChange"
                                        />
                                    </label>
                                    <button
                                        type="button"
                                        @click="removeImage"
                                        class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- 画像なし → アップロードエリア -->
                            <div v-else class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-gray-400 transition-colors">
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

                    <!-- Public URL -->
                    <div class="bg-white rounded-xl shadow-lg p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
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
                        <a
                            v-if="page.status === 'published'"
                            :href="`/careers/${page.slug}`"
                            target="_blank"
                            class="text-sm hover:underline"
                            :style="{ color: colors.teal }"
                        >
                            プレビュー →
                        </a>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-all disabled:opacity-50"
                            :style="{ backgroundColor: colors.teal }"
                        >
                            {{ form.processing ? '保存中...' : '更新する' }}
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
