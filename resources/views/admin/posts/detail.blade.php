@extends('layouts.admin')

@section('title')
    Post #{{ $post->id }}
@endsection

@section('content')
<div class="w-full max-w-4xl">
    <a href="{{ route('admin.posts.index') }}" class="text-xs font-bold uppercase tracking-tight text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 mb-6 inline-block">
        ← Voltar
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Imagem e Conteúdo -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-950 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
            <div class="aspect-square bg-gray-50 dark:bg-zinc-950">
                <img src="{{ $post->image_url }}" 
                     alt=""
                     class="w-full h-full object-cover">
            </div>

            <div class="p-6">
                <!-- Autor -->
                <div class="flex items-center gap-3 pb-6 border-b border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.users.detail', $post->users->id) }}">
                        <img src="{{ $post->users->profile_photo_url ?? '' }}" 
                             alt="{{ $post->users->username }}"
                             class="w-12 h-12 rounded-full object-cover border border-gray-100 dark:border-gray-700">
                    </a>
                    <div>
                        <a href="{{ route('admin.users.detail', $post->users->id) }}" class="font-black text-sm uppercase tracking-tight text-gray-900 dark:text-white hover:underline">
                            {{ $post->users->username }}
                        </a>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-bold mt-1">{{ $post->created_at ? $post->created_at->format('d/m/Y H:i') : '' }}</p>
                    </div>
                </div>

                <!-- Descrição -->
                @if ($post->description)
                    <p class="text-gray-700 dark:text-gray-300 mt-6">{{ $post->description }}</p>
                @endif
            </div>
        </div>

        <!-- Informações e Ações -->
        <div class="space-y-6">
            <!-- Métricas -->
            <div class="bg-white dark:bg-zinc-950 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4">Métricas</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">Curtidas</p>
                        <p class="text-lg font-black text-gray-900 dark:text-white">{{ $post->likes_count ?? 0 }}</p>
                    </div>
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">Status Mod.</p>
                        <p class="text-xs font-black uppercase tracking-tight text-gray-900 dark:text-white">{{ $post->moderation_status }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">NSFW</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">{{ $post->is_nsfw ? '⚠️ Sim' : '✓ Não' }}</p>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="bg-white dark:bg-zinc-950 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4">Ações</p>
                <div class="space-y-2">
                    @if ($post->moderation_status === 'POSSIBLE')
                        <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST">
                            @csrf
                            <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-green-600 bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-900 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl transition-colors">
                                Aprovar
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este post?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-900 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl transition-colors">
                            Deletar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Post ID -->
            <div class="bg-gray-50 dark:bg-zinc-950 border border-gray-100 dark:border-gray-700 rounded-xl p-4">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-bold">Post ID</p>
                <p class="text-gray-900 dark:text-gray-300 font-mono text-xs mt-2 break-all">{{ $post->id }}</p>
            </div>
        </div>
    </div>
</div>
@endsection