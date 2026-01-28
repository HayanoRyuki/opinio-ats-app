<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    auth: Object,
});
</script>

<template>
    <Head title="レポート" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    レポート
                </h1>

                <div class="card">
                    <div class="p-6">
                        <p class="text-gray-500 text-center py-8">
                            レポート機能は準備中です
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
