@props(['post'])

<div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md group">
    <a href="{{ route('admin.posts.detail', $post->id) }}" class="block">
        <div class="aspect-square bg-gray-50 overflow-hidden">
            <img src="{{ $post->image_url }}" 
                 alt=""
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </div>
    </a>

    <div class="p-5">
        <div class="flex items-center gap-2 mb-3">
            <a href="{{ route('admin.users.detail', $post->users->id) }}" class="flex-shrink-0">
                <img src="{{ $post->users->profile_photo_url ?? asset('images/icons/icon.png') }}" 
                     alt="{{ $post->users->username }}"
                     class="w-7 h-7 rounded-lg object-cover border border-gray-100">
            </a>
            <a href="{{ route('admin.users.detail', $post->users->id) }}" class="flex-1 min-w-0">
                <p class="text-xs font-black uppercase tracking-tight text-gray-900 truncate hover:underline">
                    {{ $post->users->username }}
                </p>
            </a>
        </div>

        @if ($post->description)
            <p class="text-xs text-gray-600 line-clamp-2 mb-3">{{ $post->description }}</p>
        @endif

        <div class="flex items-center justify-between text-xs text-gray-400 font-bold mb-4">
            <span>{{ $post->created_at ? $post->created_at->format('d/m/Y H:i') : '' }}</span>
            <span>{{ $post->likes_count ?? 0 }} curtidas</span>
        </div>

        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="w-full" onsubmit="return confirm('Deletar este post?')">
            @csrf
            @method('DELETE')
            <button class="w-full py-2 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 border border-red-100 hover:bg-red-100 rounded-lg transition-colors">
                Deletar
            </button>
        </form>
    </div>
</div>