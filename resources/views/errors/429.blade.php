@extends('layouts.public')

@section('content')
<div class="flex flex-1 flex-col items-center justify-center px-4">
    <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-700" viewBox="0 0 64 64" fill="currentColor">
        <path d="M32 8L10 52h44L32 8zm0 8l14 28H18L32 16zm-2 16v8h4v-8h-4zm0 12v4h4v-4h-4z" />
    </svg>
    <h1 class="mt-6 font-black text-7xl tracking-tighter text-zinc-900 dark:text-white">429</h1>
    <h2 class="mt-2 font-bold text-xl text-zinc-700 dark:text-zinc-300">Muitas requisições</h2>
    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 text-center max-w-md">
        Você fez muitas requisições em pouco tempo. Aguarde um momento e tente novamente.
    </p>
    @auth
        <a href="{{ route('feed') }}"
           class="mt-8 px-6 py-3 text-xs font-bold uppercase tracking-tight rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-80 transition-all">
            Tentar novamente
        </a>
    @else
        <a href="/"
           class="mt-8 px-6 py-3 text-xs font-bold uppercase tracking-tight rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-80 transition-all">
            Ir para o início
        </a>
    @endauth
</div>
@endsection
