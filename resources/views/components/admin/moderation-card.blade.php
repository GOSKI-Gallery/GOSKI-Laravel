@props(['post'])

@php
    $isNSFW = $post->is_nsfw || $post->moderation_status === 'POSSIBLE';
@endphp

<div class="bg-white dark:bg-zinc-950 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md group">
    <div class="relative aspect-square bg-gray-50 dark:bg-zinc-900 overflow-hidden">
        @if ($isNSFW)
            <div id="nsfw-overlay-{{ $post->id }}" class="absolute inset-0 bg-black/40 backdrop-blur-lg z-10 flex items-center justify-center">
                <div class="text-center">
                    <p class="text-white font-black text-sm uppercase tracking-wider">⚠️ NSFW</p>
                    <button onclick="document.getElementById('nsfw-overlay-{{ $post->id }}').classList.add('hidden'); document.getElementById('nsfw-img-{{ $post->id }}').classList.remove('blur-3xl')" class="mt-2 px-4 py-2 text-[10px] font-black uppercase tracking-tight text-white bg-white/20 hover:bg-white/30 border border-white/40 rounded-lg transition-colors">
                        Visualizar
                    </button>
                </div>
            </div>
        @endif
        <img id="nsfw-img-{{ $post->id }}" src="{{ $post->image_url }}" 
             alt=""
             class="w-full h-full object-cover {{ $isNSFW ? 'blur-3xl' : '' }} group-hover:scale-105 transition-transform duration-300">
    </div>

    <div class="p-3">
        <div class="flex items-center gap-2 mb-2">
            <img src="{{ $post->users->profile_photo_url ?? '' }}" 
                 alt="{{ $post->users->username }}"
                 class="w-6 h-6 rounded-full object-cover border border-gray-100 dark:border-gray-700 flex-shrink-0"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%239ca3af%22%3E%3Cpath d=%22M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z%22/%3E%3C/svg%3E'">
            <div class="flex-1 min-w-0">
                <p class="font-black text-[10px] uppercase tracking-tight text-gray-900 dark:text-white truncate">{{ $post->users->username }}</p>
                <p class="text-[9px] text-gray-400 dark:text-gray-500 font-bold">{{ $post->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @if ($post->description)
            <p class="text-[10px] text-gray-600 dark:text-gray-400 line-clamp-2 mb-2">{{ $post->description }}</p>
        @endif

        <div class="flex items-center gap-1 text-[9px] text-gray-400 dark:text-gray-500 font-bold mb-2">
            <span>{{ $post->likes_count ?? 0 }} curtidas</span>
            <span>·</span>
            <span class="uppercase tracking-tight">{{ $post->moderation_status }}</span>
        </div>

        @if ($post->moderation_status === 'POSSIBLE')
            <div class="flex gap-1.5">
                <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="w-full py-1.5 text-[9px] font-black uppercase tracking-tight text-green-600 bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-900 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg transition-colors">
                        Aprovar
                    </button>
                </form>
                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Deletar este post?')">
                    @csrf
                    @method('DELETE')
                    <button class="w-full py-1.5 text-[9px] font-black uppercase tracking-tight text-red-600 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-900 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-lg transition-colors">
                        Deletar
                    </button>
                </form>
            </div>
        @else
            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Deletar este post?')">
                @csrf
                @method('DELETE')
                <button class="w-full py-1.5 text-[9px] font-black uppercase tracking-tight text-red-600 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-900 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-lg transition-colors">
                    Deletar
                </button>
            </form>
        @endif
    </div>
</div>