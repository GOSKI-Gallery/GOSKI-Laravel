@props([
    'action',
    'user' => [],
    'buttonText' => 'Salvar Alterações',
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
    <div class="mx-auto mt-6 w-full max-w-sm mb-2">
        <form action="{{ $action }}" method="{{ $method === 'POST' ? 'POST' : 'GET' }}" class="space-y-4"
            enctype="multipart/form-data">
            @if ($method !== 'POST' && $method !== 'GET')
                @method($method)
            @endif
            @csrf
@endif

@if ($showAvatar)
    <div class="mb-6">
        <div id="upload-area-edit"
            class="group relative flex h-80 w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 transition-all hover:border-black/50 hover:bg-gray-100/30">

            <div class="{{ isset($user['avatar_url']) ? 'hidden' : '' }} p-4 text-center transition-transform group-hover:scale-105"
                id="upload-placeholder-edit">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-white shadow-sm">
                    <svg class="h-7 w-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-600">Clique para alterar foto</p>
                <p class="mt-1 text-[11px] uppercase tracking-tighter text-gray-400">PNG, JPG ou GIF até 10MB</p>
            </div>

            <input type="file" name="image_url" id="image_url_edit"
                class="absolute inset-0 z-20 cursor-pointer opacity-0" accept="image/*">

            <img id="image-preview-edit" src="{{ $user['avatar_url'] ?? '' }}"
                class="{{ isset($user['avatar_url']) ? '' : 'hidden' }} absolute inset-0 z-10 h-full w-full object-cover" />
        </div>
    </div>
@endif

@if ($showUsername)
    <div class="flex flex-col gap-1.5 mb-2">
        <div class="group relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <img class="h-4 w-4 opacity-30 transition-opacity group-focus-within:opacity-100"
                    src="{{ asset('images/icons/icon.png') }}" alt="User Icon">
            </div>
            <input type="text" name="username" id="username" placeholder="Nome de usuário"
                value="{{ old('username', $user['username'] ?? '') }}"
                class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-gray-400 focus:border-gray-500 focus:ring-4 focus:ring-gray-500/10">
        </div>
        @error('username')
            <p class="ml-2 text-[10px] font-bold uppercase tracking-tight text-red-500">{{ $message }}</p>
        @enderror
    </div>
@endif

@if ($showEmail)
    <div class="flex flex-col gap-1.5 mb-2">
        <div class="group relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <img class="h-4 w-4 opacity-30 transition-opacity group-focus-within:opacity-100"
                    src="{{ asset('images/icons/email.png') }}" alt="Email Icon">
            </div>
            <input type="email" name="email" id="email" placeholder="E-mail"
                value="{{ old('email', $user['email'] ?? '') }}"
                class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-gray-400 focus:border-gray-500 focus:ring-4 focus:ring-gray-500/10">
        </div>
        @error('email')
            <p class="ml-2 text-[10px] font-bold uppercase tracking-tight text-red-500">{{ $message }}</p>
        @enderror
    </div>
@endif

@if ($showPassword)
    <div class="flex flex-col gap-1.5 mb-2">
        <div class="group relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <img class="h-4 w-4 opacity-30 transition-opacity group-focus-within:opacity-100"
                    src="{{ asset('images/icons/lock.png') }}" alt="Lock Icon">
            </div>
            <input type="password" name="password" id="password" placeholder="Nova Senha"
                class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-gray-400 focus:border-gray-500 focus:ring-4 focus:ring-gray-500/10">
        </div>
        @error('password')
            <p class="ml-2 text-[10px] font-bold uppercase tracking-tight text-red-500">{{ $message }}</p>
        @enderror
    </div>
@endif

@if ($showPasswordConfirmation)
    <div class="flex flex-col gap-1.5 mb-2">
        <div class="group relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <img class="h-4 w-4 opacity-30 transition-opacity group-focus-within:opacity-100"
                    src="{{ asset('images/icons/lock.png') }}" alt="Lock Icon">
            </div>
            <input type="password" name="password_confirmation" placeholder="Confirme a nova senha"
                class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-gray-400 focus:border-gray-500 focus:ring-4 focus:ring-gray-500/10">
        </div>
    </div>
@endif

@if ($wrapInForm)
    @if ($showButton)
        <button type="submit"
            class="mt-2 w-full cursor-pointer rounded-xl bg-gray-900 py-3 font-bold text-white shadow-lg shadow-gray-200 transition-all hover:bg-gray-600 hover:shadow-gray-100 active:scale-[0.98]">
            {{ $buttonText }}
        </button>
    @endif
    </form>
    </div>
@endif

@if (!$wrapInForm && $showButton)
    <button type="submit"
        class="mt-2 w-full cursor-pointer rounded-xl bg-gray-900 py-3 font-bold text-white shadow-lg shadow-gray-200 transition-all hover:bg-gray-600 hover:shadow-gray-100 active:scale-[0.98]">
        {{ $buttonText }}
    </button>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('image_url_edit');
        const preview = document.getElementById('image-preview-edit');
        const placeholder = document.getElementById('upload-placeholder-edit');

        if (input && preview) {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        if (placeholder) {
                            placeholder.classList.add('hidden');
                        }
                    }

                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
