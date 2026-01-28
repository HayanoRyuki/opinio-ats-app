<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Opinio ATS
            </h1>
            <p class="text-lg text-gray-600 mb-8">
                採用管理システム
            </p>
            <div class="space-x-4">
                <a
                    href="/login"
                    class="btn btn-primary"
                >
                    ログイン
                </a>
                <a
                    href="/dashboard"
                    class="btn btn-secondary"
                >
                    ダッシュボード
                </a>
            </div>
        </div>
    </div>
</template>
