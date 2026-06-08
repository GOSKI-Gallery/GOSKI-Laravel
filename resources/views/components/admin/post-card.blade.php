@props(['post'])

<div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md group">
    <a href="{{ route('admin.posts.detail', $post->id) }}" class="block">
        <div class="aspect-square bg-gray-50 dark:bg-gray-900 overflow-hidden">
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
                     class="w-7 h-7 rounded-xl object-cover border border-gray-100 dark:border-gray-700"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%239ca3af%22%3E%3Cpath d=%22M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z%22/%3E%3C/svg%3E'">
            </a>
            <a href="{{ route('admin.users.detail', $post->users->id) }}" class="flex-1 min-w-0">
                <p class="text-xs font-black uppercase tracking-tight text-gray-900 dark:text-white truncate hover:underline">
                    {{ $post->users->username }}
                </p>
            </a>
        </div>

        @if ($post->description)
            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">{{ $post->description }}</p>
        @endif

        <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500 font-bold mb-4">
            <span>{{ $post->created_at ? $post->created_at->format('d/m/Y H:i') : '' }}</span>
            <span>{{ $post->likes_count ?? 0 }} curtidas</span>
        </div>

        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="w-full" onsubmit="return confirm('Deletar este post?')">
            @csrf
            @method('DELETE')
            <button class="w-full py-2 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-900 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl transition-colors">
                Deletar
            </button>
        </form>
    </div>
</div>