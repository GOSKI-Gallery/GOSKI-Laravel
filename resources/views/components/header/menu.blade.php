<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
<el-dropdown class="inline-block">
    <button class="flex items-center gap-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 px-2 py-1 rounded-full transition cursor-pointer">
        <img class="w-8 h-8 rounded-full border border-[var(--border-color)] object-cover bg-[var(--bg-avatar)]"
            src="{{ Auth::user()->profile_picture ?? asset('images/icons/icon.png') }}" alt="ProfilePicture">
        <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-[var(--icon-primary)]">
            <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" />
        </svg>
    </button>

    <el-menu anchor="bottom end" popover class="bg-[var(--bg-menu)] shadow-lg shadow-black/20 rounded-md border border-[var(--border-color)] w-56">
        <div class="py-1">
            @if(isset(Auth::user()->role) && Auth::user()->role === 'admin')
                <a href="/admin"
                    class="flex items-center justify-between px-4 py-3 focus:outline-hidden text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-zinc-100 dark:hover:bg-zinc-800 text-sm">
                    <h1 class="text-lg font-bold text-[var(--text-primary)]">Dashboard</h1>
                </a>
            @else
                <a href="/profile"
                    class="flex items-center justify-between px-4 py-3 focus:outline-hidden text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-zinc-100 dark:hover:bg-zinc-800 text-sm">
                    <h1 class="text-lg font-bold text-[var(--text-primary)]">Meu perfil</h1>
                </a>
            @endif

            <form action="/logout" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-4 py-3 focus:outline-hidden w-full text-sm text-left cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" class="text-red-600">
                        <path d="M11 1H2C0.9 1 0 1.9 0 3V17C0 18.1 0.9 19 2 19H11C12.1 19 13 18.1 13 17V14H11V17H2V3H11V6H13V3C13 1.9 12.1 1 11 1ZM18.59 9L14.83 5.24L13.41 6.66L16.17 9.42H5V11.42H16.17L13.41 14.18L14.83 15.6L18.59 11.84C19.37 11.06 19.37 9.79 18.59 9Z" fill="currentColor"/>
                    </svg>
                    <h1 class="text-lg font-bold text-red-600">Sair</h1>
                </button>
            </form>
        </div>
    </el-menu>
</el-dropdown>