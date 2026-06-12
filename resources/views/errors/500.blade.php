@extends('layouts.public')

@section('content')
<div class="flex flex-1 flex-col items-center justify-center px-4">
    <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-700" viewBox="0 0 64 64" fill="currentColor">
        <circle cx="32" cy="32" r="18" fill="none" stroke="currentColor" stroke-width="4" />
        <path d="M32 14v12l4 4-4 4v16" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
        <path d="M24 24l12 12M36 24L24 36" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
    </svg>
    <h1 class="mt-6 font-black text-7xl tracking-tighter text-zinc-900 dark:text-white">500</h1>
    <h2 class="mt-2 font-bold text-xl text-zinc-700 dark:text-zinc-300">Erro interno do servidor</h2>
    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 text-center max-w-md">
        Algo deu errado do nosso lado. A equipe foi notificada.
    </p>
    @auth
        <a href="{{ route('feed') }}"
           class="mt-8 px-6 py-3 text-xs font-bold uppercase tracking-tight rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-80 transition-all">
            Ir para o feed
        </a>
    @else
        <a href="/"
           class="mt-8 px-6 py-3 text-xs font-bold uppercase tracking-tight rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-80 transition-all">
            Ir para o início
        </a>
    @endauth
</div>
@endsection
