@props(['user'])

<div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md group">
    <a href="{{ route('admin.users.detail', $user->id) }}" class="block">
        <div class="aspect-square bg-gray-50 dark:bg-gray-900 overflow-hidden">
            <img src="{{ $user->profile_photo_url ?? '' }}"
                 alt="{{ $user->username }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%239ca3af%22%3E%3Cpath d=%22M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z%22/%3E%3C/svg%3E'">
        </div>
    </a>

    <div class="p-5">
        <a href="{{ route('admin.users.detail', $user->id) }}" class="block">
            <h3 class="font-black text-gray-900 dark:text-white text-sm uppercase tracking-tight hover:underline">
                {{ $user->username }}
            </h3>
        </a>

        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
            <div>
                <p class="text-gray-400 dark:text-gray-500 font-bold">Seguidores</p>
                <p class="text-gray-900 dark:text-white font-black mt-1">{{ $user->followers_count ?? 0 }}</p>
            </div>
            <div>
                <p class="text-gray-400 dark:text-gray-500 font-bold">Seguindo</p>
                <p class="text-gray-900 dark:text-white font-black mt-1">{{ $user->following_count ?? 0 }}</p>
            </div>
            <div>
                <p class="text-gray-400 dark:text-gray-500 font-bold">Posts</p>
                <p class="text-gray-900 dark:text-white font-black mt-1">{{ $user->posts_count ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>