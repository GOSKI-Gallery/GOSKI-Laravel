@php
    $appName = config('app.name', 'GOSKI');
    $currentRoute = Route::currentRouteName();
@endphp

<header class="sticky top-0 z-40 w-full bg-[var(--bg-header)] border-b border-zinc-200 dark:border-zinc-800">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center cursor-pointer">
            <a href="/admin" class="flex items-center group text-zinc-900 dark:text-white">
                <svg class="w-8 h-8" viewBox="0 0 64 64" fill="currentColor">
                    <path d="M 32 7 C 17.348 7 10 18.056922 10 31.669922 C 10 37.045922 14.980469 43.231469 14.980469 45.105469 C 14.980469 46.508785 12.18794 47.707398 10.785156 48.220703 C 10.782973 48.221502 10.77952 48.221861 10.777344 48.222656 L 5 50 L 5.4433594 52.658203 C 5.7643594 54.586203 7.4336719 56 9.3886719 56 L 54.611328 56 C 56.566328 56 58.234641 54.586203 58.556641 52.658203 L 59 50 L 53.636719 48.349609 L 53.619141 48.34375 C 51.405814 47.567816 50 46.63975 50 45 C 50 43.126 54 39.153 54 35 C 54 18.058 43.322234 7 32.615234 7 C 32.615234 7.0074641 32.617123 7.0140502 32.617188 7.0214844 C 32.411441 7.0145228 32.210626 7 32 7 z M 32 11 C 37.528882 11 40.250748 13.680925 41.953125 16.527344 C 43.655502 19.373763 44.013672 22.236328 44.013672 22.236328 L 44.15625 23.421875 L 45.265625 23.859375 C 45.265625 23.859375 48 24.786637 48 28 C 48 29.744236 47.031833 30.842779 45.40625 32.242188 C 43.780667 33.641596 41.548122 34.975755 40.339844 37.478516 L 40.339844 37.480469 C 39.712331 38.780544 39.608076 40.071285 39.333984 41.660156 C 39.059893 43.249027 38.733192 45.018354 38.203125 46.615234 C 37.673058 48.212115 36.942318 49.600707 35.992188 50.519531 C 35.160822 51.323461 34.193011 51.854546 32.681641 51.974609 C 32.458094 51.987193 32.234549 52 32 52 C 31.765451 52 31.541906 51.987193 31.318359 51.974609 C 29.806989 51.854545 28.839178 51.323502 28.007812 50.519531 C 27.057682 49.600707 26.326942 48.212115 25.796875 46.615234 C 25.266808 45.018354 24.940107 43.249027 24.666016 41.660156 C 24.391924 40.071285 24.287669 38.780544 23.660156 37.480469 L 23.660156 37.478516 C 22.451878 34.975802 20.219333 33.641596 18.59375 32.242188 C 16.968167 30.842779 16 29.744236 16 28 C 16 24.786637 18.734375 23.859375 18.734375 23.859375 L 19.84375 23.421875 L 19.986328 22.236328 C 19.986328 22.236328 20.344498 19.373763 22.046875 16.527344 C 23.749252 13.680925 26.471118 11 32 11 z M 28.5 19 C 26.83 19 27.477938 22.480172 23.960938 24.701172 C 23.032938 25.287172 21 25.789 21 27.5 C 21 28.774 22.087 30 24 30 C 26.715 30 30 25.089156 30 21.410156 C 30 19.836156 29.399 19 28.5 19 z M 35.5 19 C 34.601 19 34 19.836156 34 21.410156 C 34 25.089156 37.285 30 40 30 C 41.913 30 43 28.774 43 27.5 C 43 25.789 40.967062 25.287172 40.039062 24.701172 C 36.522062 22.480172 37.17 19 35.5 19 z M 32 27.5 C 31 27.5 29 30.247 29 32 C 29 32.713 29.695 33 30 33 C 31.28 33 31.36 32 32 32 C 32.64 32 32.72 33 34 33 C 34.305 33 35 32.713 35 32 C 35 30.247 33 27.5 32 27.5 z M 32 35 C 29.6 35 28 36.8 28 38 C 28 39.428 28.770172 39.269625 29.076172 41.515625 C 29.568172 45.120625 30.529 49 32 49 C 33.471 49 34.431828 45.120625 34.923828 41.515625 C 35.229828 39.269625 36 39.428 36 38 C 36 36.8 34.4 35 32 35 z" />
                </svg>
                <h1 class="ml-3 font-black text-lg tracking-tight">{{ config('app.name') }}</h1>
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
            <a href="{{ route('feed') }}" target="_blank" rel="noopener"
               class="ml-1 px-4 py-2 text-xs font-black uppercase tracking-tight rounded-xl transition-all text-white bg-gradient-to-r from-[#FF0000] via-[#AF054D] to-[#1B0EDB] hover:opacity-90 active:scale-[0.98]">
                Ver Feed
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
