<header class="flex justify-between items-center h-16 px-8">
    <div class='flex items-center'>
        <a href='/' class='flex items-center group text-zinc-900 dark:text-white'> 
            <svg class="w-auto h-10 transition-transform group-hover:scale-110" viewBox="0 0 64 64" fill="currentColor">
                <path d="M 32 7 C 17.348 7 10 18.056922 10 31.669922 C 10 37.045922 14.980469 43.231469 14.980469 45.105469 C 14.980469 46.508785 12.18794 47.707398 10.785156 48.220703 C 10.782973 48.221502 10.77952 48.221861 10.777344 48.222656 L 5 50 L 5.4433594 52.658203 C 5.7643594 54.586203 7.4336719 56 9.3886719 56 L 54.611328 56 C 56.566328 56 58.234641 54.586203 58.556641 52.658203 L 59 50 L 53.636719 48.349609 L 53.619141 48.34375 C 51.405814 47.567816 50 46.63975 50 45 C 50 43.126 54 39.153 54 35 C 54 18.058 43.322234 7 32.615234 7 C 32.615234 7.0074641 32.617123 7.0140502 32.617188 7.0214844 C 32.411441 7.0145228 32.210626 7 32 7 z M 32 11 C 37.528882 11 40.250748 13.680925 41.953125 16.527344 C 43.655502 19.373763 44.013672 22.236328 44.013672 22.236328 L 44.15625 23.421875 L 45.265625 23.859375 C 45.265625 23.859375 48 24.786637 48 28 C 48 29.744236 47.031833 30.842779 45.40625 32.242188 C 43.780667 33.641596 41.548122 34.975755 40.339844 37.478516 L 40.339844 37.480469 C 39.712331 38.780544 39.608076 40.071285 39.333984 41.660156 C 39.059893 43.249027 38.733192 45.018354 38.203125 46.615234 C 37.673058 48.212115 36.942318 49.600707 35.992188 50.519531 C 35.160822 51.323461 34.193011 51.854546 32.681641 51.974609 C 32.458094 51.987193 32.234549 52 32 52 C 31.765451 52 31.541906 51.987193 31.318359 51.974609 C 29.806989 51.854545 28.839178 51.323502 28.007812 50.519531 C 27.057682 49.600707 26.326942 48.212115 25.796875 46.615234 C 25.266808 45.018354 24.940107 43.249027 24.666016 41.660156 C 24.391924 40.071285 24.287669 38.780544 23.660156 37.480469 L 23.660156 37.478516 C 22.451878 34.975802 20.219333 33.641596 18.59375 32.242188 C 16.968167 30.842779 16 29.744236 16 28 C 16 24.786637 18.734375 23.859375 18.734375 23.859375 L 19.84375 23.421875 L 19.986328 22.236328 C 19.986328 22.236328 20.344498 19.373763 22.046875 16.527344 C 23.749252 13.680925 26.471118 11 32 11 z M 28.5 19 C 26.83 19 27.477938 22.480172 23.960938 24.701172 C 23.032938 25.287172 ... (line truncated to 2000 chars)
            </svg>
            <h1 class='ml-3 text-3xl font-black tracking-tighter'>{{ config('app.name') }}</h1>
        </a>
    </div>

    <div class="flex items-center gap-3">
        <x-ui.theme-toggle id="theme-toggle-auth" class="p-2" />

        @if(request()->routeIs('landingPage'))
            <x-ui.button
                href="/register"
                variant="outline"
                size="sm">
                Entrar
            </x-ui.button>
        @elseif(request()->is('register'))
            <x-ui.button
                href="/"
                variant="outline"
                size="sm">
                Faça seu login
            </x-ui.button>
        @endif
    </div>
</header>

@push('scripts')
<script>
    if (!window.themeToggleAuthLoaded) {
        window.themeToggleAuthLoaded = true;

        const toggleBtn = document.getElementById('theme-toggle-auth');
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
    }
</script>
@endpush
