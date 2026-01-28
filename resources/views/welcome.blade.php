<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 p-6">

        {{-- 共通メニュー --}}
        <x-navbar />

        {{-- メインコンテンツ --}}
        <main class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8 w-full max-w-2xl text-center">
            <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Welcome to Laravel!</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">This is your starter page. You can modify it as you like.</p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="https://laravel.com/docs" target="_blank"
                   class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Documentation</a>
                <a href="https://laracasts.com" target="_blank"
                   class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Laracasts</a>
            </div>
        </main>
    </div>
</x-app-layout>
