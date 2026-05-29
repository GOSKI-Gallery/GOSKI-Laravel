@extends('layouts.admin')

@section('title')
    Post #{{ $post->id }}
@endsection

@section('content')
<div class="w-full max-w-4xl">
    <a href="{{ route('admin.posts.index') }}" class="text-xs font-bold uppercase tracking-tight text-gray-400 hover:text-gray-600 mb-6 inline-block">
        ← Voltar
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Imagem e Conteúdo -->
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <div class="aspect-square bg-gray-50">
                <img src="{{ $post->image_url }}" 
                     alt=""
                     class="w-full h-full object-cover">
            </div>

            <div class="p-6">
                <!-- Autor -->
                <div class="flex items-center gap-3 pb-6 border-b border-gray-100">
                    <a href="{{ route('admin.users.detail', $post->users->id) }}">
                        <img src="{{ $post->users->profile_photo_url ?? asset('images/icons/icon.png') }}" 
                             alt="{{ $post->users->username }}"
                             class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                    </a>
                    <div>
                        <a href="{{ route('admin.users.detail', $post->users->id) }}" class="font-black text-sm uppercase tracking-tight text-gray-900 hover:underline">
                            {{ $post->users->username }}
                        </a>
                        <p class="text-xs text-gray-400 font-bold mt-1">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- Descrição -->
                @if ($post->description)
                    <p class="text-gray-700 mt-6">{{ $post->description }}</p>
                @endif
            </div>
        </div>

        <!-- Informações e Ações -->
        <div class="space-y-6">
            <!-- Métricas -->
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Métricas</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <p class="text-xs text-gray-500 font-bold">Curtidas</p>
                        <p class="text-lg font-black text-gray-900">{{ $post->likes_count ?? 0 }}</p>
                    </div>
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <p class="text-xs text-gray-500 font-bold">Status Mod.</p>
                        <p class="text-xs font-black uppercase tracking-tight text-gray-900">{{ $post->moderation_status }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500 font-bold">NSFW</p>
                        <p class="text-xs font-bold">{{ $post->is_nsfw ? '⚠️ Sim' : '✓ Não' }}</p>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Ações</p>
                <div class="space-y-2">
                    @if ($post->moderation_status === 'POSSIBLE')
                        <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST">
                            @csrf
                            <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-green-600 bg-green-50 border border-green-100 hover:bg-green-100 rounded-lg transition-colors">
                                Aprovar
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este post?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 border border-red-100 hover:bg-red-100 rounded-lg transition-colors">
                            Deletar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Post ID -->
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                <p class="text-xs text-gray-400 font-bold">Post ID</p>
                <p class="text-gray-900 font-mono text-xs mt-2 break-all">{{ $post->id }}</p>
            </div>
        </div>
    </div>
</div>
@endsection