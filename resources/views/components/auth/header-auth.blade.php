<header class="w-full bg-[var(--bg-header)] border-b border-[var(--border-color)]">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-center">
        <a href="/feed" class="flex items-center group">
            <img class="w-auto h-10" src="{{ asset('images/logo.svg') }}" alt="GOSKI">
            <h1 class="ml-2 font-black text-3xl tracking-tighter text-[var(--text-primary)]">
                {{ config('app.name') }}
            </h1>
        </a>
    </div>
</header>