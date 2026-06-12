@extends('layouts.public')

@section('content')
<div class="flex flex-1 flex-col items-center justify-center px-4">
    <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-700" viewBox="0 0 64 64" fill="currentColor">
        <path d="M24 12l16 16-8 8-16-16zm14 14l8 8-6 6-8-8z" />
        <circle cx="32" cy="34" r="18" fill="none" stroke="currentColor" stroke-width="4" />
        <path d="M20 28l12 12M32 28L20 40" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
    </svg>
    <h1 class="mt-6 font-black text-7xl tracking-tighter text-zinc-900 dark:text-white">503</h1>
    <h2 class="mt-2 font-bold text-xl text-zinc-700 dark:text-zinc-300">Em manutenção</h2>
    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 text-center max-w-md">
        Estamos realizando melhorias no sistema. Voltamos em breve!
    </p>
    <a href="/"
       class="mt-8 px-6 py-3 text-xs font-bold uppercase tracking-tight rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-80 transition-all">
        Tentar novamente
    </a>
</div>
@endsection
