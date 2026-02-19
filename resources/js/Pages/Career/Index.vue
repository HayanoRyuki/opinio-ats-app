<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    company: Object,
    pages: Array,
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
</script>

<template>
    <Head :title="`${company.name} - 採用情報`" />

    <div class="min-h-screen bg-gray-50">
        <!-- ===== Hero ===== -->
        <div
            class="relative overflow-hidden"
            :style="{ background: `linear-gradient(135deg, ${colors.primary} 0%, ${colors.teal} 100%)` }"
        >
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full"
                    style="background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.2) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(255,255,255,0.15) 0%, transparent 40%);">
                </div>
            </div>

            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <span class="font-bold text-2xl" :style="{ color: colors.primary }">
                            {{ company.name?.charAt(0) || 'C' }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ company.name }}</h1>
                        <p v-if="company.industry" class="text-white/70 text-sm mt-0.5">{{ company.industry }}</p>
                    </div>
                </div>
                <p class="text-white/90 text-lg">採用情報</p>
            </div>
        </div>

        <!-- ===== Job Listings ===== -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

            <!-- 件数表示 -->
            <div class="mb-6 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    <span class="font-bold text-lg" :style="{ color: colors.primary }">{{ pages.length }}</span>
                    件の募集中ポジション
                </p>
            </div>

            <!-- 求人カード一覧 -->
            <div v-if="pages.length > 0" class="space-y-4">
                <Link
                    v-for="page in pages"
                    :key="page.id"
                    :href="`/careers/${page.slug}`"
                    class="block bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group border border-gray-100"
                >
                    <div class="flex flex-col sm:flex-row">
                        <!-- アイキャッチ画像（あれば） -->
                        <div
                            v-if="page.featured_image"
                            class="sm:w-48 h-40 sm:h-auto flex-shrink-0"
                        >
                            <img
                                :src="`/storage/${page.featured_image}`"
                                class="w-full h-full object-cover"
                                :alt="page.title"
                            />
                        </div>

                        <!-- テキスト情報 -->
                        <div class="flex-1 p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <!-- タイトル -->
                                    <h2
                                        class="text-lg font-bold group-hover:underline line-clamp-2"
                                        :style="{ color: colors.primary }"
                                    >
                                        {{ page.title }}
                                    </h2>

                                    <!-- メタ情報 -->
                                    <div class="flex flex-wrap items-center gap-3 mt-3">
                                        <span
                                            v-if="page.job?.employment_type"
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full"
                                            :style="{ backgroundColor: colors.teal + '15', color: colors.teal }"
                                        >
                                            {{ employmentTypes[page.job.employment_type] || page.job.employment_type }}
                                        </span>

                                        <span v-if="page.job?.location" class="inline-flex items-center text-sm text-gray-500">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ page.job.location }}
                                        </span>

                                        <span v-if="page.job?.salary_min || page.job?.salary_max" class="inline-flex items-center text-sm text-gray-500">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <template v-if="page.job.salary_min && page.job.salary_max">{{ page.job.salary_min }}万〜{{ page.job.salary_max }}万円</template>
                                            <template v-else-if="page.job.salary_min">{{ page.job.salary_min }}万円〜</template>
                                            <template v-else>〜{{ page.job.salary_max }}万円</template>
                                        </span>
                                    </div>
                                </div>

                                <!-- 矢印 -->
                                <div class="flex-shrink-0 hidden sm:flex items-center justify-center w-10 h-10 rounded-full group-hover:bg-gray-100 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- 求人なし -->
            <div v-else class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-500">現在募集中のポジションはありません</h3>
                <p class="mt-1 text-sm text-gray-400">新しい募集が開始されましたらこちらに掲載されます。</p>
            </div>
        </div>

        <!-- ===== Footer ===== -->
        <footer class="border-t border-gray-200 bg-white mt-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
                <p class="text-sm text-gray-400">
                    Powered by <span :style="{ color: colors.primary }" class="font-semibold">Opinio ATS</span>
                </p>
            </div>
        </footer>
    </div>
</template>
