<div class="w-full max-w-sm mt-6 mx-auto">
    <form action="{{ route('authenticate') }}" method="POST" class="space-y-4">
        @csrf

        <x-ui.input
            type="email"
            name="email"
            id="email"
            placeholder="Seu e-mail"
            value="{{ old('email') }}"
            required
            icon="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
            error="{{ $errors->first('email') }}"
        />

        <x-ui.input
            type="password"
            name="password"
            id="password"
            placeholder="Sua senha"
            required
            icon="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9 2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"
            error="{{ $errors->first('password') }}"
        />

        <x-ui.button
            type="submit"
            variant="primary"
            size="md"
            class="mt-2 w-full shadow-lg shadow-zinc-200 dark:shadow-none hover:shadow-zinc-100">
            Entrar
        </x-ui.button>
    </form>
</div>
