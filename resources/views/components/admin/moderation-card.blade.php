@props(['post'])

@php
    $isNSFW = $post->is_nsfw || $post->moderation_status === 'POSSIBLE';
@endphp

<div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
    <div class="flex gap-4 p-5 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
        <img src="{{ $post->users->profile_photo_url ?? asset('images/icons/icon.png') }}" 
             alt="{{ $post->users->username }}"
             class="w-12 h-12 rounded-xl object-cover border border-gray-100 dark:border-gray-700"
             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%239ca3af%22%3E%3Cpath d=%22M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z%22/%3E%3C/svg%3E'">
        <div class="flex-1">
            <p class="font-black text-sm uppercase tracking-tight text-gray-900 dark:text-white">{{ $post->users->username }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-bold mt-1">{{ $post->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="relative aspect-square bg-gray-50 dark:bg-gray-900 overflow-hidden">
        @if ($isNSFW)
            <div class="absolute inset-0 bg-black/40 backdrop-blur-lg z-10 flex items-center justify-center">
                <div class="text-center">
                    <p class="text-white font-black text-lg uppercase tracking-wider">⚠️ Conteúdo NSFW</p>
                    <p class="text-white/60 text-xs font-bold mt-2">Desfocado por segurança</p>
                </div>
            </div>
        @endif
        <img src="{{ $post->image_url }}" 
             alt=""
             class="w-full h-full object-cover {{ $isNSFW ? 'blur-3xl' : '' }}">
    </div>

    <div class="p-5">
        @if ($post->description)
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">{{ $post->description }}</p>
        @endif

        <div class="grid grid-cols-2 gap-3 text-center text-xs mb-4">
            <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                <p class="text-gray-400 dark:text-gray-500 font-bold">Curtidas</p>
                <p class="text-gray-900 dark:text-white font-black mt-1">{{ $post->likes_count ?? 0 }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                <p class="text-gray-400 dark:text-gray-500 font-bold">Status</p>
                <p class="text-gray-900 dark:text-white font-black mt-1 uppercase tracking-tight">{{ $post->moderation_status }}</p>
            </div>
        </div>

        @if ($post->moderation_status === 'POSSIBLE')
            <div class="flex gap-3">
                <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-green-600 bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-900 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl transition-colors">
                        Aprovar
                    </button>
                </form>
                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Deletar este post?')">
                    @csrf
                    @method('DELETE')
                    <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-900 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl transition-colors">
                        Deletar
                    </button>
                </form>
            </div>
        @else
            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Deletar este post?')">
                @csrf
                @method('DELETE')
                <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-900 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl transition-colors">
                    Deletar
                </button>
            </form>
        @endif
    </div>
</div>