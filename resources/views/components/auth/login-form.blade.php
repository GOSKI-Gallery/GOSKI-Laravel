<form method="POST" action="{{ route('authenticate') }}" class="flex flex-col items-center w-full max-w-sm mx-auto gap-4">
    @csrf

    <h2 class="text-2xl font-bold text-[var(--text-primary)] mb-2">Faça seu login.</h2>

    <div class="flex items-center rounded-xl w-full h-14 px-4 bg-zinc-200 dark:bg-zinc-800">
        <svg class="w-5 h-5 mr-3 opacity-30 text-[var(--icon-primary)]" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 8L12 13L4 8V6L12 11L20 6V8Z"/>
        </svg>
        <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email') }}"
            placeholder="Email"
            required
            class="flex-1 bg-transparent text-black dark:text-white font-bold text-center focus:outline-none placeholder:text-zinc-400"
        />
        <div class="w-5 h-5 ml-3 opacity-0"></div>
    </div>
    @error('email')
        <p class="text-red-600 text-sm w-full text-center">{{ $message }}</p>
    @enderror

    <div class="flex items-center rounded-xl w-full h-14 px-4 bg-zinc-200 dark:bg-zinc-800">
        <svg class="w-5 h-5 mr-3 opacity-30 text-[var(--icon-primary)]" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8ZM12 17C10.9 17 10 16.1 10 15C10 13.9 10.9 13 12 13C13.1 13 14 13.9 14 15C14 16.1 13.1 17 12 17ZM15.1 8H8.9V6C8.9 4.29 10.29 2.9 12 2.9C13.71 2.9 15.1 4.29 15.1 6V8Z"/>
        </svg>
        <input
            type="password"
            name="password"
            id="password"
            placeholder="Senha"
            required
            class="flex-1 bg-transparent text-black dark:text-white font-bold text-center focus:outline-none placeholder:text-zinc-400"
        />
        <div class="w-5 h-5 ml-3 opacity-0"></div>
    </div>
    @error('password')
        <p class="text-red-600 text-sm w-full text-center">{{ $message }}</p>
    @enderror

    <button type="submit"
        class="w-full h-14 bg-zinc-900 dark:bg-zinc-200 text-white dark:text-zinc-900 font-bold text-lg rounded-xl border border-zinc-900 dark:border-zinc-200 hover:opacity-80 active:opacity-80 transition-all duration-200 cursor-pointer px-20 mt-2">
        Entrar
    </button>
</form>