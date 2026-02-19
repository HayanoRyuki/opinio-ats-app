<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

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
const isJobOpen = computed(() => props.job && props.job.status === 'open');

// スクロールでヘッダー固定
const isScrolled = ref(false);
const handleScroll = () => {
    isScrolled.value = window.scrollY > 360;
};
onMounted(() => window.addEventListener('scroll', handleScroll));
onUnmounted(() => window.removeEventListener('scroll', handleScroll));

// フォーム
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

// セクション内スクロール
const scrollToSection = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};
</script>

<template>
    <Head :title="page.title" />

    <div class="min-h-screen bg-gray-50">
        <!-- ===== Hero Section ===== -->
        <div
            class="relative overflow-hidden"
            :style="{ background: `linear-gradient(135deg, ${colors.primary} 0%, ${colors.teal} 100%)` }"
        >
            <!-- パターン背景 -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full"
                    style="background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.2) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(255,255,255,0.15) 0%, transparent 40%);">
                </div>
            </div>

            <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
                <!-- 会社ロゴ＋名前 -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <span class="font-bold text-xl" :style="{ color: colors.primary }">O</span>
                    </div>
                    <div>
                        <span class="text-white font-bold text-lg">Opinio</span>
                        <span
                            v-if="job?.employment_type"
                            class="ml-3 inline-flex px-3 py-0.5 text-xs font-medium rounded-full bg-white/20 text-white border border-white/30"
                        >
                            {{ employmentTypes[job.employment_type] || job.employment_type }}
                        </span>
                    </div>
                </div>

                <!-- 求人タイトル -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight mb-4">
                    {{ page.title }}
                </h1>

                <!-- メタ情報 -->
                <div class="flex flex-wrap items-center gap-4 mt-6">
                    <span v-if="job?.location" class="inline-flex items-center text-sm text-white/80">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ job.location }}
                    </span>
                    <span v-if="job?.salary_min || job?.salary_max" class="inline-flex items-center text-sm text-white/80">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <template v-if="job.salary_min && job.salary_max">年収 {{ job.salary_min }}万〜{{ job.salary_max }}万円</template>
                        <template v-else-if="job.salary_min">年収 {{ job.salary_min }}万円〜</template>
                        <template v-else>〜年収 {{ job.salary_max }}万円</template>
                    </span>
                </div>
            </div>
        </div>

        <!-- ===== Section Tabs (Sticky) ===== -->
        <div
            class="bg-white border-b border-gray-200 sticky top-0 z-40 transition-shadow"
            :class="{ 'shadow-md': isScrolled }"
        >
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center -mb-px overflow-x-auto">
                    <button
                        @click="scrollToSection('section-about')"
                        class="tab-btn whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent transition-colors hover:border-gray-300 hover:text-gray-700 text-gray-500"
                    >
                        ポジションについて
                    </button>
                    <button
                        @click="scrollToSection('section-details')"
                        class="tab-btn whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent transition-colors hover:border-gray-300 hover:text-gray-700 text-gray-500"
                    >
                        募集要項
                    </button>
                    <button
                        v-if="isJobOpen"
                        @click="scrollToSection('section-apply')"
                        class="tab-btn whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent transition-colors hover:border-gray-300 hover:text-gray-700 text-gray-500"
                    >
                        応募する
                    </button>
                </nav>
            </div>
        </div>

        <!-- ===== Main Content (2 Column) ===== -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-8">

                <!-- Left: Main Content -->
                <div class="flex-1 min-w-0">

                    <!-- Success / Error Messages -->
                    <div
                        v-if="submitted || flash.success"
                        class="mb-6 p-5 rounded-xl bg-white shadow border-l-4"
                        :style="{ borderColor: colors.green }"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.green + '15' }">
                                <svg class="w-5 h-5" :style="{ color: colors.green }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold" :style="{ color: colors.primary }">応募を受け付けました</h3>
                                <p class="text-sm text-gray-600 mt-0.5">ご応募ありがとうございます。担当者よりご連絡いたします。</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="form.errors.duplicate || form.errors.job" class="mb-6 p-4 rounded-xl bg-red-50 border-l-4 border-red-400">
                        <p class="text-sm text-red-700">{{ form.errors.duplicate || form.errors.job }}</p>
                    </div>

                    <!-- Section: ポジションについて -->
                    <section id="section-about" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                        <div class="p-6 sm:p-8">
                            <div
                                class="career-content"
                                v-html="page.content"
                            ></div>
                        </div>
                    </section>

                    <!-- Section: 募集要項テーブル -->
                    <section id="section-details" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                        <div class="p-6 sm:p-8">
                            <h2 class="section-heading">募集要項</h2>

                            <table class="w-full text-sm">
                                <tbody>
                                    <tr class="border-b border-gray-100">
                                        <th class="py-4 pr-4 text-left font-semibold text-gray-600 w-36 align-top">職種</th>
                                        <td class="py-4 text-gray-800">{{ job?.title || page.title }}</td>
                                    </tr>
                                    <tr class="border-b border-gray-100">
                                        <th class="py-4 pr-4 text-left font-semibold text-gray-600 w-36 align-top">雇用形態</th>
                                        <td class="py-4 text-gray-800">{{ employmentTypes[job?.employment_type] || '-' }}</td>
                                    </tr>
                                    <tr v-if="job?.location" class="border-b border-gray-100">
                                        <th class="py-4 pr-4 text-left font-semibold text-gray-600 w-36 align-top">勤務地</th>
                                        <td class="py-4 text-gray-800">{{ job.location }}</td>
                                    </tr>
                                    <tr v-if="job?.salary_min || job?.salary_max" class="border-b border-gray-100">
                                        <th class="py-4 pr-4 text-left font-semibold text-gray-600 w-36 align-top">給与</th>
                                        <td class="py-4 text-gray-800">
                                            <template v-if="job.salary_min && job.salary_max">年収 {{ job.salary_min }}万円 〜 {{ job.salary_max }}万円</template>
                                            <template v-else-if="job.salary_min">年収 {{ job.salary_min }}万円 〜</template>
                                            <template v-else>〜 年収 {{ job.salary_max }}万円</template>
                                        </td>
                                    </tr>
                                    <tr v-if="job?.description" class="border-b border-gray-100">
                                        <th class="py-4 pr-4 text-left font-semibold text-gray-600 w-36 align-top">仕事内容</th>
                                        <td class="py-4 text-gray-800 whitespace-pre-wrap">{{ job.description }}</td>
                                    </tr>
                                    <tr v-if="job?.requirements" class="border-b border-gray-100">
                                        <th class="py-4 pr-4 text-left font-semibold text-gray-600 w-36 align-top">応募資格</th>
                                        <td class="py-4 text-gray-800 whitespace-pre-wrap">{{ job.requirements }}</td>
                                    </tr>
                                    <tr v-if="job?.benefits">
                                        <th class="py-4 pr-4 text-left font-semibold text-gray-600 w-36 align-top">福利厚生</th>
                                        <td class="py-4 text-gray-800 whitespace-pre-wrap">{{ job.benefits }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Section: 応募フォーム -->
                    <section
                        v-if="isJobOpen && !submitted"
                        id="section-apply"
                        class="bg-white rounded-xl shadow-sm overflow-hidden mb-6"
                    >
                        <div class="p-6 sm:p-8">
                            <h2 class="section-heading">応募する</h2>
                            <p class="text-sm text-gray-600 mb-6">
                                ご興味をお持ちいただけましたら、以下のフォームからご応募ください。カジュアル面談からでも歓迎です。
                            </p>

                            <form @submit.prevent="submitApplication" class="space-y-5 max-w-lg">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        お名前 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all text-sm"
                                        :style="{ '--tw-ring-color': colors.teal }"
                                        placeholder="山田 太郎"
                                    />
                                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        メールアドレス <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all text-sm"
                                        placeholder="example@email.com"
                                    />
                                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">電話番号</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all text-sm"
                                        placeholder="090-1234-5678"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">自己PR・メッセージ</label>
                                    <textarea
                                        v-model="form.message"
                                        rows="4"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition-all text-sm resize-y"
                                        placeholder="ご経験やアピールポイントがあればご記入ください"
                                    ></textarea>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full sm:w-auto px-10 py-3 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-all disabled:opacity-50"
                                    :style="{ backgroundColor: colors.green }"
                                >
                                    {{ form.processing ? '送信中...' : '応募する' }}
                                </button>
                            </form>
                        </div>
                    </section>
                </div>

                <!-- Right: Sidebar (Sticky) -->
                <div class="lg:w-80 flex-shrink-0">
                    <div class="lg:sticky lg:top-20 space-y-4">
                        <!-- 応募ボタン -->
                        <button
                            v-if="isJobOpen && !submitted"
                            @click="scrollToSection('section-apply')"
                            class="w-full py-4 text-base font-bold rounded-xl text-white shadow-lg hover:shadow-xl transition-all"
                            :style="{ backgroundColor: colors.green }"
                        >
                            応募する
                        </button>

                        <div v-if="submitted" class="w-full py-4 text-base font-bold rounded-xl text-center text-white" :style="{ backgroundColor: colors.teal }">
                            応募済み
                        </div>

                        <!-- 求人情報サマリー -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="border-b border-gray-100 px-5 py-3">
                                <h3 class="font-bold text-sm" :style="{ color: colors.primary }">求人情報</h3>
                            </div>
                            <div class="px-5 py-4 space-y-3 text-sm">
                                <div v-if="job?.employment_type">
                                    <dt class="text-gray-500 text-xs mb-0.5">雇用形態</dt>
                                    <dd class="text-gray-800 font-medium">{{ employmentTypes[job.employment_type] }}</dd>
                                </div>
                                <div v-if="job?.location">
                                    <dt class="text-gray-500 text-xs mb-0.5">勤務地</dt>
                                    <dd class="text-gray-800 font-medium">{{ job.location }}</dd>
                                </div>
                                <div v-if="job?.salary_min || job?.salary_max">
                                    <dt class="text-gray-500 text-xs mb-0.5">給与</dt>
                                    <dd class="text-gray-800 font-medium">
                                        <template v-if="job.salary_min && job.salary_max">{{ job.salary_min }}万〜{{ job.salary_max }}万円</template>
                                        <template v-else-if="job.salary_min">{{ job.salary_min }}万円〜</template>
                                        <template v-else>〜{{ job.salary_max }}万円</template>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 text-xs mb-0.5">ステータス</dt>
                                    <dd>
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                                            :style="{
                                                backgroundColor: isJobOpen ? colors.green + '20' : '#ef444420',
                                                color: isJobOpen ? colors.green : '#ef4444'
                                            }"
                                        >
                                            {{ isJobOpen ? '募集中' : '募集終了' }}
                                        </span>
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <!-- 会社情報 -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="border-b border-gray-100 px-5 py-3">
                                <h3 class="font-bold text-sm" :style="{ color: colors.primary }">会社情報</h3>
                            </div>
                            <div class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                        :style="{ backgroundColor: colors.primary }"
                                    >
                                        <span class="text-white font-bold">O</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm" :style="{ color: colors.primary }">Opinio</p>
                                        <p class="text-xs text-gray-500">採用をフェアに。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ===== Footer ===== -->
        <footer class="border-t border-gray-200 bg-white mt-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
                <p class="text-sm text-gray-400">
                    Powered by <span :style="{ color: colors.primary }" class="font-semibold">Opinio ATS</span>
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Section Heading (HRMOS-style left border) */
.section-heading {
    font-size: 1.5rem;
    font-weight: 700;
    color: #332c54;
    padding-left: 1rem;
    border-left: 4px solid #65b891;
    margin-bottom: 1.5rem;
}

/* CMS Content Styles */
.career-content :deep(h2) {
    font-size: 1.35rem;
    font-weight: 700;
    color: #332c54;
    padding-left: 1rem;
    border-left: 4px solid #4e878c;
    margin: 2.5rem 0 1rem;
}

.career-content :deep(h2:first-child) {
    margin-top: 0;
}

.career-content :deep(h3) {
    font-size: 1.15rem;
    font-weight: 600;
    color: #332c54;
    padding-left: 0.75rem;
    border-left: 3px solid #65b891;
    margin: 1.75rem 0 0.75rem;
}

.career-content :deep(p) {
    margin: 0.75rem 0;
    line-height: 1.9;
    color: #374151;
    font-size: 0.95rem;
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
    line-height: 1.8;
    color: #374151;
    font-size: 0.95rem;
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
    border-top: 1px solid #e5e7eb;
    margin: 2rem 0;
}

/* Tab active indicator */
.tab-btn:hover {
    color: #332c54;
}
</style>
