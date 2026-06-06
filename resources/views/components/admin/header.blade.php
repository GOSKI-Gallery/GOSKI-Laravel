@php
    $appName = config('app.name', 'GOSKI');
    $currentRoute = Route::currentRouteName();
@endphp

<header class="sticky top-0 z-40 w-full bg-[var(--bg-header)] border-b border-[var(--border-color)]">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center hover:opacity-60 cursor-pointer">
            <a href="/feed" class="flex items-center group">
                <img class="w-auto h-10 transition-transform" src="{{ asset('images/logo.svg') }}" alt="GOSKI">
                <h1 class="ml-2 font-black text-2xl tracking-tighter text-[var(--text-primary)]">
                    {{ config('app.name') }}
                </h1>
            </a>
        </div>

        <nav class="hidden md:flex items-center gap-0.5">
            <a href="{{ route('admin.dashboard') }}"
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight rounded-lg transition-all {{ $currentRoute === 'admin.dashboard' ? 'text-[var(--text-primary)] bg-zinc-100 dark:bg-zinc-800' : 'text-[var(--text-tertiary)] hover:text-[var(--text-primary)] hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.users') ? 'text-[var(--text-primary)] bg-zinc-100 dark:bg-zinc-800' : 'text-[var(--text-tertiary)] hover:text-[var(--text-primary)] hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                Usuarios
            </a>
            <a href="{{ route('admin.posts.index') }}"
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.posts') ? 'text-[var(--text-primary)] bg-zinc-100 dark:bg-zinc-800' : 'text-[var(--text-tertiary)] hover:text-[var(--text-primary)] hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                Posts
            </a>
        </nav>

        <div class="flex items-center gap-3">
            <button onclick="__toggleTheme()" class="hover:opacity-60 transition-all cursor-pointer text-[var(--icon-primary)]" title="Alternar tema">
                <svg class="dark:hidden" width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3C12 4.2 11.5 5.3 10.6 6.2C9.7 7.1 8.6 7.6 7.4 7.6C6.2 7.6 5.1 7.1 4.2 6.2C3.5 7.7 3.3 9.4 3.7 11C4.1 12.6 5.1 14 6.4 15C7.7 16 9.3 16.6 11 16.6C12.7 16.6 14.3 16 15.6 15C16.9 14 17.9 12.6 18.3 11C18.7 9.4 18.5 7.7 17.8 6.2C16.9 7.1 15.8 7.6 14.6 7.6C13.4 7.6 12.3 7.1 11.4 6.2C10.5 5.3 10 4.2 10 3H12Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                </svg>
                <svg class="hidden dark:block text-yellow-400" width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="4" fill="currentColor"/>
                    <path d="M12 2V4M12 20V22M4.93 4.93L6.34 6.34M17.66 17.66L19.07 19.07M2 12H4M20 12H22M6.34 17.66L4.93 19.07M19.07 4.93L17.66 6.34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="px-4 py-2 text-xs font-bold uppercase tracking-tight text-[var(--text-tertiary)] border border-[var(--border-color)] rounded-lg hover:text-[var(--text-primary)] hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all">
                    Sair
                </button>
            </form>
        </div>
    </div>
</header>