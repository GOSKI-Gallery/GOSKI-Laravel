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
            class="group relative flex h-80 w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 transition-all hover:border-black/50 hover:bg-zinc-100/30">

            <div class="{{ isset($user['profile_photo_url']) ? 'hidden' : '' }} p-4 text-center transition-transform group-hover:scale-105"
                id="upload-placeholder-edit">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-white dark:bg-zinc-700 shadow-sm">
                    <svg class="h-7 w-7 text-zinc-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Clique para alterar foto</p>
                <p class="mt-1 text-[11px] uppercase tracking-tighter text-zinc-400 dark:text-zinc-500">PNG, JPG ou GIF até 10MB</p>
            </div>

            <input type="file" name="image_url" id="image_url_edit"
                class="absolute inset-0 z-20 cursor-pointer opacity-0" accept="image/*">

            <img id="image-preview-edit" src="{{ $user['profile_photo_url'] ?? '' }}"
                class="{{ isset($user['profile_photo_url']) ? '' : 'hidden' }} absolute inset-0 z-10 h-full w-full object-cover" />
        </div>
    </div>
@endif

@if ($showUsername)
    <div class="flex flex-col gap-1.5 mb-2">
        <div class="group relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <svg class="w-4 h-4 text-zinc-400 group-focus-within:text-zinc-600 dark:group-focus-within:text-zinc-300 transition-colors" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                </svg>
            </div>
            <input type="text" name="username" id="username" placeholder="Nome de usuário"
                value="{{ old('username', $user['username'] ?? '') }}"
                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-zinc-400 focus:border-zinc-500 focus:ring-4 focus:ring-zinc-500/10 dark:text-zinc-200">
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
                <svg class="w-4 h-4 text-zinc-400 group-focus-within:text-zinc-600 dark:group-focus-within:text-zinc-300 transition-colors" viewBox="0 0 24 24" fill="none">
                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="currentColor"/>
                </svg>
            </div>
            <input type="email" name="email" id="email" placeholder="E-mail"
                value="{{ old('email', $user['email'] ?? '') }}"
                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-zinc-400 focus:border-zinc-500 focus:ring-4 focus:ring-zinc-500/10 dark:text-zinc-200">
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
                <svg class="w-4 h-4 text-zinc-400 group-focus-within:text-zinc-600 dark:group-focus-within:text-zinc-300 transition-colors" viewBox="0 0 24 24" fill="none">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z" fill="currentColor"/>
                </svg>
            </div>
            <input type="password" name="password" id="password" placeholder="Nova Senha"
                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-zinc-400 focus:border-zinc-500 focus:ring-4 focus:ring-zinc-500/10 dark:text-zinc-200">
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
                <svg class="w-4 h-4 text-zinc-400 group-focus-within:text-zinc-600 dark:group-focus-within:text-zinc-300 transition-colors" viewBox="0 0 24 24" fill="none">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z" fill="currentColor"/>
                </svg>
            </div>
            <input type="password" name="password_confirmation" placeholder="Confirme a nova senha"
                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-zinc-400 focus:border-zinc-500 focus:ring-4 focus:ring-zinc-500/10 dark:text-zinc-200">
        </div>
    </div>
@endif

@if ($wrapInForm)
    @if ($showButton)
        <button type="submit"
            class="mt-2 w-full cursor-pointer rounded-xl bg-zinc-900 dark:bg-zinc-950 py-3 font-bold text-white shadow-lg shadow-zinc-200 dark:shadow-none transition-all hover:bg-zinc-600 hover:shadow-zinc-100 active:scale-[0.98]">
            {{ $buttonText }}
        </button>
    @endif
    </form>
    </div>
@endif

@if (!$wrapInForm && $showButton)
    <button type="submit"
        class="mt-2 w-full cursor-pointer rounded-xl bg-zinc-900 py-3 font-bold text-white shadow-lg shadow-zinc-200 dark:shadow-none transition-all hover:bg-zinc-600 hover:shadow-zinc-100 active:scale-[0.98]">
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
