@props([
    'action',
    'user' => [],
    'buttonText' => 'Cadastrar',
    'wrapInForm' => true,
    'showUsername' => true,
    'showEmail' => true,
    'showPassword' => false,
    'showPasswordConfirmation' => false,
    'showButton' => true,
    'method' => 'POST',
    'showAvatar' => false,
])

@if ($wrapInForm)
<div class="w-full max-w-sm mt-6 mx-auto">
    <form action="{{ $action }}" method="{{ $method === 'POST' ? 'POST' : 'GET' }}" class="space-y-4" enctype="multipart/form-data">
        @if($method !== 'POST' && $method !== 'GET')
            @method($method)
        @endif
        @csrf
@endif

        @if ($showAvatar)
            <div class="mb-6">
                <div id="upload-area"
                    class="group relative flex h-80 w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 transition-all hover:border-black/50 hover:bg-gray-100/30">

                    <div class="p-4 text-center transition-transform group-hover:scale-105" id="upload-placeholder">
                        <div
                            class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-white shadow-sm">
                            <svg class="h-7 w-7 text-gray-900" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-600">Escolher foto</p>
                        </p>
                    </div>

                    <input type="file" name="avatar" id="avatar"
                        class="absolute inset-0 z-20 cursor-pointer opacity-0" accept="image/*">

                    <img id="image-preview" class="absolute inset-0 z-10 hidden h-full w-full object-cover" />
                </div>
            </div>
        @endif
        
        @if ($showUsername)
        <div class="flex flex-col gap-1.5">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <img class="opacity-30 w-4 h-4 group-focus-within:opacity-100 transition-opacity" src="{{ asset('images/icons/icon.png') }}" alt="User Icon">
                </div>
                <input type="text" name="username" id="username" placeholder="Nome de usuário"
                    value="{{ old('username', $user['username'] ?? '') }}"
                    class="w-full bg-white border border-gray-200 rounded-xl py-3 pl-11 pr-4 text-sm outline-none focus:ring-4 focus:ring-gray-500/10 focus:border-gray-500 transition-all placeholder:text-gray-400">
            </div>
            @error('username')
                <p class="text-red-500 text-[10px] font-bold uppercase ml-2 tracking-tight">{{ $message }}</p>
            @enderror
        </div>
        @endif

        @if ($showEmail)
        <div class="flex flex-col gap-1.5">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <img class="opacity-30 w-4 h-4 group-focus-within:opacity-100 transition-opacity" src="{{ asset('images/icons/email.png') }}" alt="Email Icon">
                </div>
                <input type="email" name="email" id="email" placeholder="E-mail"
                    value="{{ old('email', $user['email'] ?? '') }}"
                    class="w-full bg-white border border-gray-200 rounded-xl py-3 pl-11 pr-4 text-sm outline-none focus:ring-4 focus:ring-gray-500/10 focus:border-gray-500 transition-all placeholder:text-gray-400">
            </div>
            @error('email')
                <p class="text-red-500 text-[10px] font-bold uppercase ml-2 tracking-tight">{{ $message }}</p>
            @enderror
        </div>
        @endif

        @if ($showPassword)
        <div class="flex flex-col gap-1.5">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <img class="opacity-30 w-4 h-4 group-focus-within:opacity-100 transition-opacity" src="{{ asset('images/icons/lock.png') }}" alt="Lock Icon">
                </div>
                <input type="password" name="password" id="password" placeholder="Senha"
                    class="w-full bg-white border border-gray-200 rounded-xl py-3 pl-11 pr-4 text-sm outline-none focus:ring-4 focus:ring-gray-500/10 focus:border-gray-500 transition-all placeholder:text-gray-400">
            </div>
            @error('password')
                <p class="text-red-500 text-[10px] font-bold uppercase ml-2 tracking-tight">{{ $message }}</p>
            @enderror
        </div>
        @endif

        @if ($showPasswordConfirmation)
        <div class="flex flex-col gap-1.5">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <img class="opacity-30 w-4 h-4 group-focus-within:opacity-100 transition-opacity" src="{{ asset('images/icons/lock.png') }}" alt="Lock Icon">
                </div>
                <input type="password" name="password_confirmation" placeholder="Confirme a senha"
                    class="w-full bg-white border border-gray-200 rounded-xl py-3 pl-11 pr-4 text-sm outline-none focus:ring-4 focus:ring-gray-500/10 focus:border-gray-500 transition-all placeholder:text-gray-400">
            </div>
        </div>
        @endif

@if ($wrapInForm)
    @if($showButton)
        <button type="submit"
            class="w-full bg-gray-900 hover:bg-gray-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-gray-200 hover:shadow-gray-100 transition-all active:scale-[0.98] cursor-pointer mt-2">
            {{ $buttonText }}
        </button>
    @endif
    </form>
</div>
@endif

@if (!$wrapInForm && $showButton)
    <button type="submit"
        class="w-full bg-gray-900 hover:bg-gray-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-gray-200 hover:shadow-gray-100 transition-all active:scale-[0.98] cursor-pointer mt-2">
        {{ $buttonText }}
    </button>
@endif