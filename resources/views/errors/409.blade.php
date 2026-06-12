@extends('layouts.public')

@section('content')
<div class="flex flex-1 flex-col items-center justify-center px-4">
    <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-700" viewBox="0 0 64 64" fill="currentColor">
        <path d="M20 20l24 24M44 20L20 44" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
        <circle cx="32" cy="32" r="18" fill="none" stroke="currentColor" stroke-width="4" />
    </svg>
    <h1 class="mt-6 font-black text-7xl tracking-tighter text-zinc-900 dark:text-white">409</h1>
    <h2 class="mt-2 font-bold text-xl text-zinc-700 dark:text-zinc-300">Conflito</h2>
    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 text-center max-w-md">
        Já existe um recurso com esses dados. Tente novamente com informações diferentes.
    </p>
    @auth
        <a href="{{ route('feed') }}"
           class="mt-8 px-6 py-3 text-xs font-bold uppercase tracking-tight rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-80 transition-all">
            Voltar ao feed
        </a>
    @else
        <a href="/"
           class="mt-8 px-6 py-3 text-xs font-bold uppercase tracking-tight rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-80 transition-all">
            Ir para o início
        </a>
    @endauth
</div>
@endsection
