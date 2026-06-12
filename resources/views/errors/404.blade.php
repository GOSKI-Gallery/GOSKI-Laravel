@extends('layouts.public')

@section('content')
<div class="flex flex-1 flex-col items-center justify-center px-4 h-100%">
    <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-700" viewBox="0 0 64 64" fill="currentColor">
        <circle cx="28" cy="28" r="14" fill="none" stroke="currentColor" stroke-width="4" />
        <path d="M38 38l12 12" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
        <path d="M28 22v12m-6-6h12" />
    </svg>
    <h1 class="mt-6 font-black text-7xl tracking-tighter text-zinc-900 dark:text-white">404</h1>
    <h2 class="mt-2 font-bold text-xl text-zinc-700 dark:text-zinc-300">Página não encontrada</h2>
    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 text-center max-w-md">
        O link que você seguiu pode estar quebrado ou a página foi removida.
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
