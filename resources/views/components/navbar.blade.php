<nav class="flex items-center justify-end gap-4 mb-6">
    @if (Route::has('login'))
        @auth
            <a href="{{ url('/dashboard') }}"
               class="px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                Dashboard
            </a>
        @else
            <a href="{{ route('login') }}"
               class="px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                Log in
            </a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                    Register
                </a>
            @endif
        @endauth
    @endif
</nav>
