<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const isSidebarOpen = ref(true);

const navigation = [
    { name: 'ダッシュボード', href: '/dashboard', icon: 'home' },
    { name: '取り込み管理', href: '/intake', icon: 'inbox' },
    { name: '候補者', href: '/candidates', icon: 'users' },
    { name: '求人', href: '/jobs', icon: 'briefcase' },
    { name: '応募', href: '/applications', icon: 'document' },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 ease-in-out',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <div class="flex items-center h-16 px-6 border-b border-gray-200">
                <span class="text-xl font-bold text-primary-600">Opinio ATS</span>
            </div>

            <nav class="p-4 space-y-1">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                >
                    {{ item.name }}
                </Link>
            </nav>
        </aside>

        <!-- Main content -->
        <div :class="['transition-all duration-200', isSidebarOpen ? 'ml-64' : 'ml-0']">
            <!-- Header -->
            <header class="sticky top-0 z-40 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <button
                        @click="isSidebarOpen = !isSidebarOpen"
                        class="p-2 rounded-lg hover:bg-gray-100"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            {{ page.props.auth?.user?.name || 'ゲスト' }}
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
