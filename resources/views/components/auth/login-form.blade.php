<div class="w-full max-w-sm mt-6 mx-auto">
    <form action="{{ route('authenticate') }}" method="POST" class="space-y-4">
        @csrf
        
        <div class="flex flex-col gap-3">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-zinc-400 group-focus-within:text-zinc-600 dark:group-focus-within:text-zinc-300 transition-colors" viewBox="0 0 24 24" fill="none">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="currentColor"/>
                    </svg>
                </div>
                <input type="email" name="email" placeholder="Seu e-mail"
                    value="{{ old('email') }}"
                    class="w-full bg-[var(--bg-input)] border border-zinc-200 dark:border-zinc-700 rounded-xl py-3 pl-11 pr-4 text-sm outline-none focus:ring-4 focus:ring-zinc-500/10 focus:border-zinc-500 transition-all placeholder:text-zinc-400 dark:text-zinc-200">
            </div>
            @error('email')
                <p class="text-red-500 text-[10px] font-bold uppercase ml-2 tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-zinc-400 group-focus-within:text-zinc-600 dark:group-focus-within:text-zinc-300 transition-colors" viewBox="0 0 24 24" fill="none">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z" fill="currentColor"/>
                    </svg>
                </div>
                <input type="password" name="password" placeholder="Sua senha"
                    class="w-full bg-[var(--bg-input)] border border-zinc-200 dark:border-zinc-700 rounded-xl py-3 pl-11 pr-4 text-sm outline-none focus:ring-4 focus:ring-zinc-500/10 focus:border-zinc-500 transition-all placeholder:text-zinc-400 dark:text-zinc-200">
            </div>
        </div>

        <button type="submit"
            class="w-full bg-zinc-900 hover:bg-zinc-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-zinc-200 dark:shadow-none hover:shadow-zinc-100 transition-all active:scale-[0.98] cursor-pointer mt-2">
            Entrar
        </button>
    </form>
</div>
