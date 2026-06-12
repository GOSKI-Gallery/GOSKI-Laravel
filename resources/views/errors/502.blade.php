@extends('layouts.public')

@section('content')
<div class="flex flex-1 flex-col items-center justify-center px-4">
    <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-700" viewBox="0 0 64 64" fill="currentColor">
        <path d="M20 34c0-6 4-10 10-10 2-6 8-10 14-8 4-4 10-3 12 3 4 0 6 3 6 7H20z" />
        <path d="M22 36h28v12H22zm4-2v-2a10 10 0 0 1 20 0v2" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
        <path d="M32 38v6m0 2v-2" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
    </svg>
    <h1 class="mt-6 font-black text-7xl tracking-tighter text-zinc-900 dark:text-white">502</h1>
    <h2 class="mt-2 font-bold text-xl text-zinc-700 dark:text-zinc-300">Serviço temporariamente indisponível</h2>
    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 text-center max-w-md">
        O servidor está sobrecarregado ou passando por instabilidade. Tente novamente mais tarde.
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
