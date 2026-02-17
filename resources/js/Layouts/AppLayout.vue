<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const activeMenu = ref(null);
const isMobileMenuOpen = ref(false);
const isUserMenuOpen = ref(false);
const isNotificationOpen = ref(false);

// 通知（確認待ちドラフト数）
const draftCount = computed(() => page.props.notifications?.draftCount || 0);

// 4つのメインメニュー構造
const navigation = [
    {
        name: '候補者',
        icon: 'users',
        description: 'この人は基準に合っているか？',
        items: [
            { name: '面接', href: '/interviews', icon: 'calendar' },
            { name: 'パイプライン', href: '/pipeline', icon: 'kanban' },
            { name: '応募一覧', href: '/applications', icon: 'document' },
            { name: '候補者一覧', href: '/candidates', icon: 'users' },
            { name: '取り込み管理', href: '/intake', icon: 'inbox' },
            { name: 'エージェント', href: '/agents', icon: 'building' },
            { name: '推薦', href: '/recommendations', icon: 'star' },
        ]
    },
    {
        name: '求人',
        icon: 'briefcase',
        description: 'どんな人を採用するのか？',
        items: [
            { name: '求人管理', href: '/jobs', icon: 'briefcase' },
        ]
    },
    {
        name: '評価',
        icon: 'clipboard',
        description: '何を基準に見極めるのか？',
        items: [
            { name: '評価基準定義', href: '/evaluations', icon: 'clipboard-check', soon: true },
            { name: 'レベル定義', href: '/levels', icon: 'list', soon: true },
            { name: '質問バンク', href: '/questions', icon: 'chat', soon: true },
        ]
    },
    {
        name: '分析',
        icon: 'chart',
        description: '採用は上手くいっているのか？',
        items: [
            { name: 'ダッシュボード', href: '/dashboard', icon: 'home' },
            { name: 'レポート', href: '/reports', icon: 'chart' },
        ]
    },
];

// 現在のURLに基づいてアクティブなメニューを判定
const currentPath = computed(() => page.url || '/');

const isItemActive = (href) => {
    return currentPath.value.startsWith(href);
};

const isMenuActive = (menu) => {
    return menu.items.some(item => isItemActive(item.href));
};

// メニューのホバー/クリック制御
const openMenu = (menuName) => {
    activeMenu.value = menuName;
};

const closeMenu = () => {
    activeMenu.value = null;
};

// クリック外でメニューを閉じる
const handleClickOutside = (event) => {
    const nav = document.getElementById('main-nav');
    const userMenu = document.getElementById('user-menu');
    const notificationMenu = document.getElementById('notification-menu');

    if (nav && !nav.contains(event.target)) {
        activeMenu.value = null;
    }
    if (userMenu && !userMenu.contains(event.target)) {
        isUserMenuOpen.value = false;
    }
    if (notificationMenu && !notificationMenu.contains(event.target)) {
        isNotificationOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="min-h-screen bg-[#f4f4ed]">
        <!-- Top Navigation Bar -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-full mx-auto">
                <div class="flex items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center px-6 h-full border-r border-gray-200">
                        <Link href="/dashboard" class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-[#332c54] rounded-lg flex items-center justify-center flex-shrink-0 shadow-md">
                                <span class="text-white font-bold text-lg">O</span>
                            </div>
                            <div class="hidden sm:flex flex-col leading-tight">
                                <span class="text-[#332c54] font-bold text-base">Opinio ATS</span>
                                <span class="text-[#4e878c] text-xs font-medium">採用管理</span>
                            </div>
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav id="main-nav" class="hidden lg:flex items-center h-full flex-1 px-4">
                        <div
                            v-for="menu in navigation"
                            :key="menu.name"
                            class="relative h-full flex items-center mx-1"
                            @mouseenter="openMenu(menu.name)"
                            @mouseleave="closeMenu"
                        >
                            <!-- Menu Button -->
                            <button
                                class="menu-button flex items-center justify-center px-6 py-2.5 text-sm font-medium transition-all duration-300 rounded-lg"
                                :class="[
                                    isMenuActive(menu)
                                        ? 'menu-active text-white bg-[#332c54]'
                                        : 'text-gray-600 hover:text-[#332c54] hover:bg-gray-50 border border-gray-200'
                                ]"
                            >
                                <!-- Menu Icon -->
                                <svg v-if="menu.icon === 'users'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <svg v-else-if="menu.icon === 'briefcase'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <svg v-else-if="menu.icon === 'clipboard'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <svg v-else-if="menu.icon === 'chart'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                {{ menu.name }}
                                <svg class="w-4 h-4 ml-2 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                v-show="activeMenu === menu.name"
                                class="absolute left-0 top-full w-72 bg-white rounded-b-lg shadow-xl border border-gray-200 overflow-hidden"
                            >
                                <!-- Menu Header -->
                                <div class="px-4 py-3 bg-[#332c54] text-white">
                                    <h3 class="font-semibold">{{ menu.name }}</h3>
                                    <p class="text-xs text-white/70 mt-0.5">{{ menu.description }}</p>
                                </div>

                                <!-- Menu Items -->
                                <div class="py-2">
                                    <Link
                                        v-for="item in menu.items"
                                        :key="item.name"
                                        :href="item.soon ? '#' : item.href"
                                        class="flex items-center px-4 py-2.5 text-sm transition-colors"
                                        :class="[
                                            item.soon
                                                ? 'text-gray-400 cursor-not-allowed'
                                                : isItemActive(item.href)
                                                    ? 'text-[#332c54] bg-[#332c54]/10 font-medium border-l-4 border-[#65b891]'
                                                    : 'text-gray-700 hover:bg-gray-50 hover:text-[#332c54]'
                                        ]"
                                        @click.prevent="item.soon ? null : undefined"
                                    >
                                        <span>{{ item.name }}</span>
                                        <span v-if="item.soon" class="ml-auto text-xs bg-gray-200 text-gray-500 px-2 py-0.5 rounded">
                                            準備中
                                        </span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Right Side: Icons -->
                    <div class="flex items-center ml-auto px-4 h-full space-x-1">
                        <!-- Notification Icon -->
                        <div id="notification-menu" class="relative">
                            <button
                                @click="isNotificationOpen = !isNotificationOpen"
                                class="relative p-2 text-gray-500 hover:text-[#332c54] hover:bg-gray-100 rounded-lg transition-colors"
                                title="通知"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <!-- Notification Badge -->
                                <span
                                    v-if="draftCount > 0"
                                    class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center text-[10px] font-bold text-white bg-[#65b891] rounded-full px-1 animate-pulse"
                                >
                                    {{ draftCount > 99 ? '99+' : draftCount }}
                                </span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div
                                v-show="isNotificationOpen"
                                class="absolute right-0 mt-1 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden"
                            >
                                <div class="px-4 py-3 bg-[#332c54] text-white">
                                    <h3 class="font-semibold">通知</h3>
                                </div>

                                <div v-if="draftCount > 0" class="p-2">
                                    <Link
                                        href="/intake/drafts"
                                        @click="isNotificationOpen = false"
                                        class="flex items-center gap-3 p-3 rounded-lg bg-[#65b891]/10 hover:bg-[#65b891]/20 transition-colors"
                                    >
                                        <div class="w-10 h-10 rounded-full bg-[#65b891] flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-[#332c54]">
                                                確認待ちドラフト
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ draftCount }}件の候補者が確認を待っています
                                            </p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </Link>
                                </div>

                                <div v-else class="p-6 text-center text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <p class="text-sm">新しい通知はありません</p>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div id="user-menu" class="relative">
                            <button
                                @click="isUserMenuOpen = !isUserMenuOpen"
                                class="p-2 text-gray-500 hover:text-[#332c54] hover:bg-gray-100 rounded-lg transition-colors"
                                title="ユーザー"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>

                            <!-- User Dropdown -->
                            <div
                                v-show="isUserMenuOpen"
                                class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50"
                            >
                                <div class="px-4 py-2 border-b border-gray-100 bg-[#332c54] text-white rounded-t-lg">
                                    <p class="text-sm font-medium">{{ page.props.auth?.user?.name || 'ゲスト' }}</p>
                                    <p class="text-xs text-white/70">{{ page.props.auth?.user?.email || '' }}</p>
                                </div>
                                <Link
                                    href="/mypage"
                                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#332c54]"
                                >
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    マイページ
                                </Link>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="flex items-center w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#332c54]"
                                >
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    ログアウト
                                </Link>
                            </div>
                        </div>

                        <!-- Settings Icon -->
                        <Link
                            href="/settings/gmail"
                            class="p-2 text-gray-500 hover:text-[#332c54] hover:bg-gray-100 rounded-lg transition-colors"
                            title="設定"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </Link>

                        <!-- Mobile Menu Button -->
                        <button
                            @click="isMobileMenuOpen = !isMobileMenuOpen"
                            class="lg:hidden ml-2 p-2 rounded-lg text-gray-500 hover:text-[#332c54] hover:bg-gray-100"
                        >
                            <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div
                v-show="isMobileMenuOpen"
                class="lg:hidden bg-white border-t border-gray-200"
            >
                <div class="max-h-[calc(100vh-4rem)] overflow-y-auto">
                    <div v-for="menu in navigation" :key="menu.name" class="border-b border-gray-100">
                        <div class="px-4 py-3 bg-[#332c54]">
                            <h3 class="text-white font-medium">{{ menu.name }}</h3>
                            <p class="text-white/60 text-xs mt-0.5">{{ menu.description }}</p>
                        </div>
                        <div class="py-1">
                            <Link
                                v-for="item in menu.items"
                                :key="item.name"
                                :href="item.soon ? '#' : item.href"
                                class="flex items-center px-6 py-2.5 text-sm"
                                :class="[
                                    item.soon
                                        ? 'text-gray-400 cursor-not-allowed'
                                        : isItemActive(item.href)
                                            ? 'text-[#332c54] bg-[#332c54]/10 font-medium border-l-4 border-[#65b891]'
                                            : 'text-gray-700 hover:text-[#332c54] hover:bg-gray-50'
                                ]"
                                @click="!item.soon && (isMobileMenuOpen = false)"
                            >
                                {{ item.name }}
                                <span v-if="item.soon" class="ml-auto text-xs bg-gray-200 text-gray-500 px-2 py-0.5 rounded">
                                    準備中
                                </span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content with top padding for fixed header -->
        <main class="pt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
/* 電流アニメーション - アクティブなメニュー */
.menu-active {
    position: relative;
    overflow: hidden;
    box-shadow: 0 0 15px rgba(101, 184, 145, 0.4), 0 0 30px rgba(78, 135, 140, 0.2);
    animation: glow 2s ease-in-out infinite;
}

.menu-active::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(101, 184, 145, 0.4),
        rgba(78, 135, 140, 0.6),
        transparent
    );
    animation: electric-flow 2s linear infinite;
}

.menu-active::after {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(
        45deg,
        #332c54,
        #4e878c,
        #65b891,
        #4e878c,
        #332c54
    );
    background-size: 400% 400%;
    border-radius: 8px;
    z-index: -1;
    animation: border-flow 3s ease infinite;
}

@keyframes electric-flow {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

@keyframes border-flow {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes glow {
    0%, 100% {
        box-shadow: 0 0 10px rgba(101, 184, 145, 0.3), 0 0 20px rgba(78, 135, 140, 0.2);
    }
    50% {
        box-shadow: 0 0 20px rgba(101, 184, 145, 0.5), 0 0 40px rgba(78, 135, 140, 0.3);
    }
}

/* ホバー時のエフェクト */
.menu-button:not(.menu-active):hover {
    border-color: #4e878c;
    box-shadow: 0 0 10px rgba(78, 135, 140, 0.2);
}
</style>
