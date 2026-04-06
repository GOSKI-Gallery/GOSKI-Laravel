<div class="w-full max-w-sm mt-6 mx-auto">
    <form action="{{ route('authenticate') }}" method="POST" class="space-y-4">
        @csrf
        
        <div class="flex flex-col gap-3">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <img class="opacity-30 w-4 h-4 group-focus-within:opacity-100 transition-opacity" src="{{ asset('images/icons/email.png') }}" alt="Email Icon">
                </div>
                <input type="email" name="email" placeholder="Seu e-mail"
                    value="{{ old('email', $user['email'] ?? '') }}"
                    class="w-full bg-white border border-gray-200 rounded-xl py-3 pl-11 pr-4 text-sm outline-none focus:ring-4 focus:ring-gray-500/10 focus:border-gray-500 transition-all placeholder:text-gray-400">
            </div>
            @error('email')
                <p class="text-red-500 text-[10px] font-bold uppercase ml-2 tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <img class="opacity-30 w-4 h-4 group-focus-within:opacity-100 transition-opacity" src="{{ asset('images/icons/lock.png') }}" alt="Lock Icon">
                </div>
                <input type="password" name="password" placeholder="Sua senha"
                    class="w-full bg-white border border-gray-200 rounded-xl py-3 pl-11 pr-4 text-sm outline-none focus:ring-4 focus:ring-gray-500/10 focus:border-gray-500 transition-all placeholder:text-gray-400">
            </div>
            @error('password')
                <p class="text-red-500 text-[10px] font-bold uppercase ml-2 tracking-tight">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-gray-900 hover:bg-gray-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-gray-200 hover:shadow-gray-100 transition-all active:scale-[0.98] cursor-pointer mt-2">
            Entrar
        </button>
    </form>
</div>