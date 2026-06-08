@props([
    'profileUser',
    'userPosts' => [],
    'followersCount' => 0,
    'followingCount' => 0,
    'isOwnProfile' => true,
    'isFollowed' => false,
])

<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-8 md:gap-16 mb-12">

        <div class="relative">
            <div class="w-20 h-20 md:w-40 md:h-40 rounded-full p-1 bg-zinc-200 dark:bg-zinc-700">
                <div class="w-full h-full rounded-full border-4 border-white dark:border-zinc-950 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img src="{{ $profileUser['profile_photo_url'] ?? '' }}"
                        class="w-full h-full object-cover"
                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%239ca3af%22%3E%3Cpath d=%22M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z%22/%3E%3C/svg%3E'">
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col items-center md:items-start">
            <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
                <h2 class="text-2xl font-light text-zinc-900 dark:text-white tracking-tight">{{ $profileUser['username'] }}</h2>

                <div class="flex gap-2">
                    @if ($isOwnProfile)
                        <button type="button" id="open-edit-profile-modal-btn"
                            class="bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 px-5 py-1.5 rounded-lg text-xs font-black transition-all active:scale-95 cursor-pointer shadow-sm">
                            Editar perfil
                        </button>
                    @else
                        <form
                            action="{{ $isFollowed ? route('user.unfollow', $profileUser['id']) : route('user.follow', $profileUser['id']) }}"
                            method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-zinc-900 text-white px-5 py-1.5 rounded-lg text-xs font-black transition-all active:scale-95 cursor-pointer uppercase tracking-tighter shadow-sm">
                                {{ $isFollowed ? 'Seguindo' : 'Seguir' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="flex gap-8 mb-6">
                <div class="flex flex-col md:flex-row items-center gap-1">
                    <span class="font-black text-zinc-900 dark:text-white">{{ count($userPosts) }}</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-sm lowercase">publicações</span>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-1 cursor-pointer hover:opacity-70">
                    <span class="font-black text-zinc-900 dark:text-white">{{ $followersCount }}</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-sm lowercase">seguidores</span>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-1 cursor-pointer hover:opacity-70">
                    <span class="font-black text-zinc-900 dark:text-white">{{ $followingCount }}</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-sm lowercase">seguindo</span>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-zinc-100 dark:border-zinc-800">
        <div class="flex justify-start gap-12">
            <div
                class="flex items-center gap-2 py-4 border-t border-zinc-900 dark:border-white -mt-px text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" fill="currentColor"/>
                </svg>
                Publicações
            </div>
        </div>
    </div>

    <x-profile.user-posts :userPosts="$userPosts" />
</div>

@if ($isOwnProfile)
    <x-profile.edit-profile-modal :user="$profileUser" />
@endif
