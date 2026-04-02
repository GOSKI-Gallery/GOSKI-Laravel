<div class="mx-auto mt-6 flex w-full max-w-sm flex-col items-center gap-4">
    <form action="/register" method="POST" class="w-full">
        @csrf
        <div class="flex flex-col items-center gap-4">
            <div class="relative w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <img class="h-5 w-5 opacity-30" src="{{ asset('images/icons/icon.png') }}" alt="User Icon">
                </div>
                <input type="text" name="username" placeholder="Usuario"
                    value="{{ old('username', $user['username'] ?? '') }}"
                    class="flex w-full items-center rounded-lg border-none bg-[#D9D9D9] py-2 text-center placeholder:text-black/30">
            </div>
            @error('username')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror

            <div class="relative w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <img class="h-5 w-5 opacity-30" src="{{ asset('images/icons/email.png') }}" alt="Email Icon">
                </div>
                <input type="email" name="email" placeholder="Email"
                    value="{{ old('email', $user['email'] ?? '') }}"
                    class="flex w-full items-center rounded-lg border-none bg-[#D9D9D9] py-2 text-center placeholder:text-black/30">
            </div>
            @error('email')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror

            <div class="relative w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <img class="h-5 w-5 opacity-30" src="{{ asset('images/icons/lock.png') }}" alt="Lock Icon">
                </div>
                <input type="password" name="password" placeholder="Senha" value="{{ old('password') }}"
                    class="flex w-full items-center rounded-lg border-none bg-[#D9D9D9] py-2 text-center placeholder:text-black/30">
            </div>
            @error('password')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror

            <div class="relative w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <img class="h-5 w-5 opacity-30" src="{{ asset('images/icons/lock.png') }}" alt="Lock Icon">
                </div>
                <input type="password" name="password_confirmation" placeholder="Confirme sua senha"
                    value="{{ old('password_confirmation') }}"
                    class="flex w-full items-center rounded-lg border-none bg-[#D9D9D9] py-2 text-center placeholder:text-black/30">
            </div>
            @error('password_confirmation')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="mt-2 w-3/4 cursor-pointer rounded-lg bg-gray-900 hover:bg-black/30 px-8 py-2 font-bold text-white shadow-md transition duration-200">
                Cadastrar
            </button>
        </div>
    </form>
</div>
