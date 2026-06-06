@extends('layouts.public')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[80vh] gap-8">
    <div class="flex flex-col items-center">
        <img class="w-auto h-16 mb-4" src="{{ asset('images/logo.svg') }}" alt="GOSKI">
        <h1 class="font-black text-5xl tracking-tighter text-[var(--text-primary)]">GOSKI</h1>
        <p class="text-[var(--text-secondary)] mt-2 text-lg">Compartilhe suas aventuras.</p>
    </div>

    <x-auth.login-form />

    <p class="text-[var(--text-tertiary)] text-sm mt-4">
        Não tem uma conta?
        <a href="{{ route('register') }}" class="text-[var(--color-link)] dark:text-[var(--color-link-dark)] font-bold">Cadastre-se</a>
    </p>
</div>
@endsection