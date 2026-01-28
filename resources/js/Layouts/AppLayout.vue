<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const isSidebarOpen = ref(true);
const isUserMenuOpen = ref(false);

const navigation = [
    { name: 'ダッシュボード', href: '/dashboard', icon: 'home' },
    { name: '取り込み管理', href: '/intake', icon: 'inbox' },
    { name: '候補者', href: '/candidates', icon: 'users' },
    { name: '求人', href: '/jobs', icon: 'briefcase' },
    { name: '応募', href: '/applications', icon: 'document' },
    { name: 'パイプライン', href: '/pipeline', icon: 'kanban' },
    { name: '面接', href: '/interviews', icon: 'calendar' },
    { name: 'レポート', href: '/reports', icon: 'chart' },
];

// クリック外でメニューを閉じる
const closeUserMenu = (event) => {
    const menu = document.getElementById('user-menu');
    if (menu && !menu.contains(event.target)) {
        isUserMenuOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeUserMenu);
});

onUnmounted(() => {
    document.removeEventListener('click', closeUserMenu);
});
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

                    <div id="user-menu" class="relative">
                        <button
                            @click="isUserMenuOpen = !isUserMenuOpen"
                            class="flex items-center space-x-2 text-sm text-gray-600 hover:text-gray-900 focus:outline-none"
                        >
                            <span>{{ page.props.auth?.user?.name || 'ゲスト' }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-show="isUserMenuOpen"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                        >
                            <Link
                                href="/mypage"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    マイページ
                                </div>
                            </Link>
                        </div>
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
