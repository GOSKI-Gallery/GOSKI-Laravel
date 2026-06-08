@php
    $appName = config('app.name', 'GOSKI');
    $currentRoute = Route::currentRouteName();
@endphp

<header class="sticky top-0 z-40 w-full bg-[var(--bg-header)] border-b border-zinc-200 dark:border-zinc-800">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center cursor-pointer">
            <a href="/feed" class="flex items-center group text-zinc-900 dark:text-white">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <h1 class="ml-3 font-black text-lg tracking-tight">Admin</h1>
            </a>
        </div>

        <nav class="hidden md:flex items-center gap-0.5">
            <a href="{{ route('admin.dashboard') }}" 
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight {{ $currentRoute === 'admin.dashboard' ? 'text-zinc-900 bg-zinc-100 dark:text-white dark:bg-zinc-800' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800' }} rounded-xl transition-all">
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight {{ str_starts_with($currentRoute, 'admin.users') ? 'text-zinc-900 bg-zinc-100 dark:text-white dark:bg-zinc-800' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800' }} rounded-xl transition-all">
                Usuários
            </a>
            <a href="{{ route('admin.posts.index') }}" 
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight {{ str_starts_with($currentRoute, 'admin.posts') ? 'text-zinc-900 bg-zinc-100 dark:text-white dark:bg-zinc-800' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800' }} rounded-xl transition-all">
                Posts
            </a>
        </nav>

        <div class="flex items-center gap-2">
            <button id="theme-toggle-admin"
                class="p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all cursor-pointer text-zinc-500 dark:text-zinc-400">
                <svg class="sun-icon w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,2L14.39,5.42C13.65,5.15 12.84,5 12,5C11.16,5 10.35,5.15 9.61,5.42L12,2M3.34,7L7.5,6.65C6.9,7.16 6.36,7.78 5.94,8.5C5.5,9.24 5.25,10 5.11,10.79L3.34,7M3.36,17L5.12,13.23C5.26,14 5.53,14.78 5.95,15.5C6.37,16.24 6.91,16.86 7.5,17.37L3.36,17M20.65,7L18.88,10.8C18.74,10 18.47,9.23 18.05,8.5C17.63,7.78 17.1,7.15 16.5,6.64L20.65,7M20.64,17L16.5,17.36C17.09,16.85 17.62,16.22 18.04,15.5C18.46,14.77 18.73,14 18.87,13.21L20.64,17M12,22L9.59,18.56C10.33,18.83 11.14,19 12,19C12.82,19 13.63,18.83 14.37,18.56L12,22Z"/>
                </svg>
                <svg class="moon-icon hidden w-5 h-5 text-zinc-900 dark:text-zinc-100" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.44,22.72 9.56,22.72 5.92,19.07C2.28,15.43 2.28,9.55 5.92,5.91C6.32,5.53 6.75,5.19 7.18,4.88C7.94,4.35 9.12,5.23 9.04,6.1C8.9,7.73 9.46,9.37 10.71,10.62C11.96,11.87 13.6,12.43 15.23,12.29C16.09,12.22 16.85,12.5 17.5,13C18.21,13.54 18.71,14.3 18.97,15.15C19.07,15.45 19.07,15.7 18.97,15.95Z"/>
                </svg>
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="px-4 py-2 text-xs font-bold uppercase tracking-tight text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all">
                    Sair
                </button>
            </form>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('theme-toggle-admin');
        if (!toggleBtn) return;

        const html = document.documentElement;
        const sunIcon = toggleBtn.querySelector('.sun-icon');
        const moonIcon = toggleBtn.querySelector('.moon-icon');

        function updateUI() {
            const isDark = html.getAttribute('data-theme') === 'dark';
            if (sunIcon) sunIcon.classList.toggle('hidden', !isDark);
            if (moonIcon) moonIcon.classList.toggle('hidden', isDark);
        }

        updateUI();

        toggleBtn.addEventListener('click', function () {
            const isDark = html.getAttribute('data-theme') === 'dark';
            const newTheme = isDark ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateUI();
        });
    });
</script>
