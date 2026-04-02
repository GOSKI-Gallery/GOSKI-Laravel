<div class="flex flex-col items-center gap-4 mx-auto mt-6 w-full max-w-sm">
    <form action="{{ route('authenticate') }}" method="POST" class="w-full">
        @csrf
        <div class="flex flex-col items-center gap-4">
            <div class="relative w-full">
                <div class="left-0 z-10 absolute inset-y-0 flex items-center bg-[#D9D9D9] pr-2 pl-3 rounded-l-lg pointer-events-none">
                    <img class="opacity-30 w-5 h-5" src="{{ asset('images/icons/icon.png') }}"
                        alt="User Icon">
                </div>
                <input type="email" name="email" placeholder="Email"
                    value="{{ old('email', $user['email'] ?? '') }}"
                    class="flex items-center bg-[#D9D9D9] py-2 border-none rounded-lg focus:outline-none focus:ring-0 w-full placeholder:text-black/30 text-center">
            </div>
            @error('email')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror

            <div class="relative w-full">
                <div class="left-0 z-10 absolute inset-y-0 flex items-center bg-[#D9D9D9] pr-2 pl-3 rounded-l-lg pointer-events-none">
                    <img class="opacity-30 w-5 h-5" src="{{ asset('images/icons/lock.png') }}"
                        alt="Lock Icon">
                </div>
                <input type="password" name="password" placeholder="Senha"
                    class="flex items-center bg-[#D9D9D9] py-2 border-none rounded-lg focus:outline-none focus:ring-0 w-full placeholder:text-black/30 text-center">
            </div>
            @error('password')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="bg-gray-900 hover:bg-black/30 shadow-md mt-2 px-8 py-2 rounded-lg w-3/4 font-bold text-white transition duration-200 cursor-pointer">
                Entrar
            </button>
        </div>
    </form>
</div>