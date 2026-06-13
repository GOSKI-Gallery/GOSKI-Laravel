@extends('layouts.admin')

@section('title')
    Dashboard
@endsection

@section('content')
<div class="w-full">
    <!-- Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-admin.metric-card 
            title="Usuários"
            :value="number_format($totalUsers)"
            href="{{ route('admin.users.index') }}"
        />
        <x-admin.metric-card 
            title="Posts"
            :value="number_format($totalPosts)"
            href="{{ route('admin.posts.index') }}"
        />
        <x-admin.metric-card 
            title="Moderação Pendente"
            :value="$pendingPosts->count()"
        />
    </div>

    <!-- Fila de Moderação -->
    <div class="bg-white dark:bg-zinc-950 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-black text-lg uppercase tracking-tight text-gray-900 dark:text-white">Fila de Moderação</h2>
        </div>

        @if($pendingPosts->isEmpty())
            <div class="p-12 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500 font-bold">✓ Nenhum post pendente</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                @foreach($pendingPosts as $post)
                    <x-admin.moderation-card :post="$post" />
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection