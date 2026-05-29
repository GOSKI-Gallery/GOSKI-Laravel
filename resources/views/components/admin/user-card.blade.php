@props(['user'])

<div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md group">
    <a href="{{ route('admin.users.detail', $user->id) }}" class="block">
        <div class="aspect-square bg-gray-50 overflow-hidden">
            <img src="{{ $user->profile_photo_url ?? asset('images/icons/icon.png') }}" 
                 alt="{{ $user->username }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </div>
    </a>

    <div class="p-5">
        <a href="{{ route('admin.users.detail', $user->id) }}" class="block">
            <h3 class="font-black text-gray-900 text-sm uppercase tracking-tight hover:underline">
                {{ $user->username }}
            </h3>
        </a>

        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
            <div>
                <p class="text-gray-400 font-bold">Seguidores</p>
                <p class="text-gray-900 font-black mt-1">{{ $user->followers_count ?? 0 }}</p>
            </div>
            <div>
                <p class="text-gray-400 font-bold">Seguindo</p>
                <p class="text-gray-900 font-black mt-1">{{ $user->following_count ?? 0 }}</p>
            </div>
            <div>
                <p class="text-gray-400 font-bold">Posts</p>
                <p class="text-gray-900 font-black mt-1">{{ $user->posts_count ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>