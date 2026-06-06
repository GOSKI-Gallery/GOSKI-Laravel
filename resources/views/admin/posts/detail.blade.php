@extends('layouts.admin')

@section('title')
    Post #{{ $post->id }}
@endsection

@section('content')
<div class="w-full max-w-4xl">
    <a href="{{ route('admin.posts.index') }}" class="text-xs font-bold uppercase tracking-tight text-[var(--text-tertiary)] hover:text-[var(--text-primary)] mb-6 inline-block">
        ← Voltar
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl shadow-sm overflow-hidden">
            <div class="aspect-square bg-[var(--bg-skeleton)]">
                <img src="{{ $post->image_url }}"
                     alt=""
                     class="w-full h-full object-cover">
            </div>

            <div class="p-6">
                <div class="flex items-center gap-3 pb-6 border-b border-[var(--border-color)]">
                    <a href="{{ route('admin.users.detail', $post->users->id) }}">
                        <img src="{{ $post->users->profile_photo_url ?? asset('images/icons/icon.png') }}"
                             alt="{{ $post->users->username }}"
                             class="w-12 h-12 rounded-lg object-cover border border-[var(--border-color)]">
                    </a>
                    <div>
                        <a href="{{ route('admin.users.detail', $post->users->id) }}" class="font-black text-sm uppercase tracking-tight text-[var(--text-primary)] hover:underline">
                            {{ $post->users->username }}
                        </a>
                        <p class="text-xs text-[var(--text-tertiary)] font-bold mt-1">{{ $post->created_at ? $post->created_at->format('d/m/Y H:i') : '' }}</p>
                    </div>
                </div>

                @if ($post->description)
                    <p class="text-[var(--text-secondary)] mt-6">{{ $post->description }}</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl shadow-sm p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-[var(--text-tertiary)] mb-4">Métricas</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--border-color)]">
                        <p class="text-xs text-[var(--text-tertiary)] font-bold">Curtidas</p>
                        <p class="text-lg font-black text-[var(--text-primary)]">{{ $post->likes_count ?? 0 }}</p>
                    </div>
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--border-color)]">
                        <p class="text-xs text-[var(--text-tertiary)] font-bold">Status Mod.</p>
                        <p class="text-xs font-black uppercase tracking-tight text-[var(--text-primary)]">{{ $post->moderation_status }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-[var(--text-tertiary)] font-bold">NSFW</p>
                        <p class="text-xs font-bold">{{ $post->is_nsfw ? 'Sim' : 'Não' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl shadow-sm p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-[var(--text-tertiary)] mb-4">Ações</p>
                <div class="space-y-2">
                    @if ($post->moderation_status === 'POSSIBLE')
                        <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST">
                            @csrf
                            <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-green-600 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/40 rounded-lg transition-colors cursor-pointer">
                                Aprovar
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este post?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg transition-colors cursor-pointer">
                            Deletar
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl p-4">
                <p class="text-xs text-[var(--text-tertiary)] font-bold">Post ID</p>
                <p class="text-[var(--text-primary)] font-mono text-xs mt-2 break-all">{{ $post->id }}</p>
            </div>
        </div>
    </div>
</div>
@endsection