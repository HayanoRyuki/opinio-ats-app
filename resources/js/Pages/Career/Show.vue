<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    page: Object,
    job: Object,
});

const colors = {
    primary: '#332c54',
    teal: '#4e878c',
    green: '#65b891',
    cream: '#f4f4ed',
};

const employmentTypes = {
    full_time: '正社員',
    part_time: 'パート・アルバイト',
    contract: '契約社員',
    intern: 'インターン',
};

const flash = computed(() => usePage().props.flash || {});
const showForm = ref(false);
const submitted = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: '',
});

const submitApplication = () => {
    form.post(`/careers/${props.page.slug}/apply`, {
        onSuccess: () => {
            submitted.value = true;
            showForm.value = false;
        },
    });
};

const isJobOpen = computed(() => props.job && props.job.status === 'open');
</script>

<template>
    <Head :title="page.title" />

    <div class="min-h-screen" :style="{ backgroundColor: colors.cream }">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 shadow-md"
                            :style="{ backgroundColor: colors.primary }"
                        >
                            <span class="text-white font-bold text-lg">O</span>
                        </div>
                        <div>
                            <span class="font-bold" :style="{ color: colors.primary }">Opinio</span>
                            <span class="text-xs block" :style="{ color: colors.teal }">採用情報</span>
                        </div>
                    </div>
                    <button
                        v-if="isJobOpen && !submitted"
                        @click="showForm = !showForm"
                        class="px-6 py-2.5 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-all"
                        :style="{ backgroundColor: colors.green }"
                    >
                        {{ showForm ? '閉じる' : 'この求人に応募する' }}
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Success Message -->
            <div
                v-if="submitted || flash.success"
                class="mb-6 p-6 rounded-xl bg-white shadow-lg border-l-4"
                :style="{ borderColor: colors.green }"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                        :style="{ backgroundColor: colors.green + '20' }"
                    >
                        <svg class="w-6 h-6" :style="{ color: colors.green }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg" :style="{ color: colors.primary }">応募を受け付けました</h3>
                        <p class="text-sm text-gray-600 mt-1">ご応募ありがとうございます。担当者よりご連絡いたしますので、しばらくお待ちください。</p>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            <div
                v-if="form.errors.duplicate || form.errors.job"
                class="mb-6 p-4 rounded-xl bg-red-50 border-l-4 border-red-400"
            >
                <p class="text-sm text-red-700">{{ form.errors.duplicate || form.errors.job }}</p>
            </div>

            <!-- Application Form (slide down) -->
            <div
                v-if="showForm && isJobOpen"
                class="mb-6 bg-white rounded-xl shadow-lg overflow-hidden"
            >
                <div
                    class="px-6 py-4"
                    :style="{ backgroundColor: colors.primary }"
                >
                    <h2 class="text-lg font-bold text-white">応募フォーム</h2>
                    <p class="text-sm text-white/70 mt-0.5">{{ page.title }}</p>
                </div>

                <form @submit.prevent="submitApplication" class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">
                            お名前 <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all"
                            placeholder="山田 太郎"
                        />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">
                            メールアドレス <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all"
                            placeholder="example@email.com"
                        />
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">
                            電話番号
                        </label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all"
                            placeholder="090-1234-5678"
                        />
                        <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" :style="{ color: colors.teal }">
                            自己PR・メッセージ
                        </label>
                        <textarea
                            v-model="form.message"
                            rows="4"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all resize-y"
                            placeholder="ご経験やアピールポイントがあればご記入ください"
                        ></textarea>
                        <p v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</p>
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-all disabled:opacity-50"
                            :style="{ backgroundColor: colors.green }"
                        >
                            {{ form.processing ? '送信中...' : '応募する' }}
                        </button>
                        <button
                            type="button"
                            @click="showForm = false"
                            class="px-6 py-3 text-sm text-gray-500 hover:text-gray-700"
                        >
                            キャンセル
                        </button>
                    </div>
                </form>
            </div>

            <!-- Page Title -->
            <div class="mb-8">
                <h1
                    class="text-3xl sm:text-4xl font-bold leading-tight"
                    :style="{ color: colors.primary }"
                >
                    {{ page.title }}
                </h1>

                <!-- Job meta info -->
                <div v-if="job" class="flex flex-wrap items-center gap-3 mt-4">
                    <span
                        v-if="job.employment_type"
                        class="inline-flex px-3 py-1 text-sm rounded-full"
                        :style="{ backgroundColor: colors.teal + '20', color: colors.teal }"
                    >
                        {{ employmentTypes[job.employment_type] || job.employment_type }}
                    </span>
                    <span
                        v-if="job.location"
                        class="inline-flex items-center text-sm text-gray-600"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ job.location }}
                    </span>
                    <span
                        v-if="job.salary_min || job.salary_max"
                        class="inline-flex items-center text-sm text-gray-600"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <template v-if="job.salary_min && job.salary_max">
                            {{ job.salary_min }}万〜{{ job.salary_max }}万円
                        </template>
                        <template v-else-if="job.salary_min">
                            {{ job.salary_min }}万円〜
                        </template>
                        <template v-else>
                            〜{{ job.salary_max }}万円
                        </template>
                    </span>
                </div>
            </div>

            <!-- Page Content -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div
                        class="career-content prose prose-lg max-w-none"
                        v-html="page.content"
                    ></div>
                </div>
            </div>

            <!-- Bottom CTA -->
            <div
                v-if="isJobOpen && !submitted"
                class="mt-8 bg-white rounded-xl shadow-lg p-6 sm:p-8 text-center"
            >
                <h2 class="text-xl font-bold mb-2" :style="{ color: colors.primary }">
                    興味を持っていただけましたか？
                </h2>
                <p class="text-gray-600 mb-6">
                    ご応募お待ちしております。まずはお気軽にご連絡ください。
                </p>
                <button
                    @click="showForm = true; window.scrollTo({ top: 0, behavior: 'smooth' })"
                    class="px-10 py-3.5 text-base font-bold rounded-lg text-white shadow-lg hover:shadow-xl transition-all"
                    :style="{ backgroundColor: colors.green }"
                >
                    この求人に応募する
                </button>
            </div>

            <!-- Not open notice -->
            <div
                v-if="!isJobOpen"
                class="mt-8 bg-white rounded-xl shadow-lg p-6 text-center"
            >
                <p class="text-gray-500">この求人は現在募集を行っていません。</p>
            </div>

            <!-- Footer -->
            <footer class="mt-12 pt-8 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-400">
                    Powered by <span :style="{ color: colors.primary }" class="font-semibold">Opinio ATS</span>
                </p>
            </footer>
        </main>
    </div>
</template>

<style scoped>
.career-content :deep(h2) {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 2rem 0 1rem;
    color: #332c54;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #65b891;
}

.career-content :deep(h3) {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 1.5rem 0 0.75rem;
    color: #332c54;
}

.career-content :deep(p) {
    margin: 0.75rem 0;
    line-height: 1.8;
    color: #374151;
}

.career-content :deep(ul),
.career-content :deep(ol) {
    margin: 0.75rem 0;
    padding-left: 1.5rem;
}

.career-content :deep(ul) {
    list-style-type: disc;
}

.career-content :deep(ol) {
    list-style-type: decimal;
}

.career-content :deep(li) {
    margin: 0.5rem 0;
    line-height: 1.7;
    color: #374151;
}

.career-content :deep(a) {
    color: #4e878c;
    text-decoration: underline;
}

.career-content :deep(strong) {
    font-weight: 700;
    color: #332c54;
}

.career-content :deep(hr) {
    border: none;
    border-top: 2px solid #e5e7eb;
    margin: 2rem 0;
}
</style>
