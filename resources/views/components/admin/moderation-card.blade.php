@props(['post'])

@php
    $isNSFW = $post->is_nsfw || $post->moderation_status === 'POSSIBLE';
@endphp

<div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
    <div class="flex gap-4 p-5 bg-gray-50 border-b border-gray-100">
        <img src="{{ $post->users->profile_photo_url ?? asset('images/icons/icon.png') }}" 
             alt="{{ $post->users->username }}"
             class="w-12 h-12 rounded-lg object-cover border border-gray-100">
        <div class="flex-1">
            <p class="font-black text-sm uppercase tracking-tight text-gray-900">{{ $post->users->username }}</p>
            <p class="text-xs text-gray-400 font-bold mt-1">{{ $post->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="relative aspect-square bg-gray-50 overflow-hidden">
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
            <p class="text-sm text-gray-700 mb-4">{{ $post->description }}</p>
        @endif

        <div class="grid grid-cols-2 gap-3 text-center text-xs mb-4">
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <p class="text-gray-400 font-bold">Curtidas</p>
                <p class="text-gray-900 font-black mt-1">{{ $post->likes_count ?? 0 }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <p class="text-gray-400 font-bold">Status</p>
                <p class="text-gray-900 font-black mt-1 uppercase tracking-tight">{{ $post->moderation_status }}</p>
            </div>
        </div>

        @if ($post->moderation_status === 'POSSIBLE')
            <div class="flex gap-3">
                <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-green-600 bg-green-50 border border-green-100 hover:bg-green-100 rounded-lg transition-colors">
                        Aprovar
                    </button>
                </form>
                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Deletar este post?')">
                    @csrf
                    @method('DELETE')
                    <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 border border-red-100 hover:bg-red-100 rounded-lg transition-colors">
                        Deletar
                    </button>
                </form>
            </div>
        @else
            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Deletar este post?')">
                @csrf
                @method('DELETE')
                <button class="w-full py-2.5 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 border border-red-100 hover:bg-red-100 rounded-lg transition-colors">
                    Deletar
                </button>
            </form>
        @endif
    </div>
</div>