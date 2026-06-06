<header class="sticky top-0 z-50 bg-[var(--bg-header)] border-b border-[var(--border-color)]">
    <div class="mx-auto px-4 max-w-6xl">
        <div class="flex justify-between items-center h-16">

            <div class="flex items-center hover:opacity-60 cursor-pointer">
                <a href="/feed" class="flex items-center group">
                    <img class="w-auto h-10 transition-transform" src="{{ asset('images/logo.svg') }}" alt="GOSKI">
                    <h1 class="ml-2 font-black text-2xl tracking-tighter text-[var(--text-primary)]">
                        {{ config('app.name') }}
                    </h1>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <button id="open-modal-btn" class="hover:opacity-60 transition-all cursor-pointer text-[var(--icon-primary)]">
                    <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                        <path d="M17.3333 6.66667H14.6667V14.6667H6.66667V17.3333H14.6667V25.3333H17.3333V17.3333H25.3333V14.6667H17.3333V6.66667Z" fill="currentColor"/>
                    </svg>
                </button>

                <button id="notification-btn" class="hover:opacity-60 transition-all cursor-pointer text-[var(--icon-primary)] relative">
                    <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                        <path d="M16 29.3333C17.4667 29.3333 18.6667 28.1333 18.6667 26.6667H13.3333C13.3333 28.1333 14.5333 29.3333 16 29.3333ZM24 21.3333V14.6667C24 10.6667 21.8667 7.33333 18 6.4V5.33333C18 4.22667 17.1067 3.33333 16 3.33333C14.8933 3.33333 14 4.22667 14 5.33333V6.4C10.1333 7.33333 8 10.6667 8 14.6667V21.3333L5.33333 24V25.3333H26.6667V24L24 21.3333Z" fill="currentColor"/>
                    </svg>
                </button>

                <button onclick="__toggleTheme()" class="hover:opacity-60 transition-all cursor-pointer text-[var(--icon-primary)]" title="Alternar tema">
                    <svg class="dark:hidden" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 3C12 4.2 11.5 5.3 10.6 6.2C9.7 7.1 8.6 7.6 7.4 7.6C6.2 7.6 5.1 7.1 4.2 6.2C3.5 7.7 3.3 9.4 3.7 11C4.1 12.6 5.1 14 6.4 15C7.7 16 9.3 16.6 11 16.6C12.7 16.6 14.3 16 15.6 15C16.9 14 17.9 12.6 18.3 11C18.7 9.4 18.5 7.7 17.8 6.2C16.9 7.1 15.8 7.6 14.6 7.6C13.4 7.6 12.3 7.1 11.4 6.2C10.5 5.3 10 4.2 10 3H12Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    </svg>
                    <svg class="hidden dark:block text-yellow-400" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="4" fill="currentColor"/>
                        <path d="M12 2V4M12 20V22M4.93 4.93L6.34 6.34M17.66 17.66L19.07 19.07M2 12H4M20 12H22M6.34 17.66L4.93 19.07M19.07 4.93L17.66 6.34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>

                <x-header.menu />
            </div>
        </div>
    </div>
</header>