@extends('layouts.public')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[calc(100vh-8rem)] px-4">
    <svg class="w-24 h-24 text-zinc-300 dark:text-zinc-700" viewBox="0 0 64 64" fill="currentColor">
        <path d="M22 28h20v24H22zm4-10a6 6 0 0 1 12 0v10h-4V18a2 2 0 0 0-4 0v10h-4V18zm6 18v6m0 2v-2" />
    </svg>
    <h1 class="mt-6 font-black text-7xl tracking-tighter text-zinc-900 dark:text-white">401</h1>
    <h2 class="mt-2 font-bold text-xl text-zinc-700 dark:text-zinc-300">Não autenticado</h2>
    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 text-center max-w-md">
        Você precisa estar logado para acessar esta página.
    </p>
    <a href="/"
       class="mt-8 px-6 py-3 text-xs font-bold uppercase tracking-tight rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-80 transition-all">
        Fazer login
    </a>
</div>
@endsection
