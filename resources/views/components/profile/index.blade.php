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
            <div class="w-20 h-20 md:w-40 md:h-40 rounded-full p-1 bg-gray-200">
                <div class="w-full h-full rounded-full border-4 border-white overflow-hidden bg-gray-100">
                    <img src="{{ $profileUser['profile_photo_url'] ?? asset('images/icons/icon.png') }}"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col items-center md:items-start">
            <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
                <h2 class="text-2xl font-light text-gray-900 tracking-tight">{{ $profileUser['username'] }}</h2>

                <div class="flex gap-2">
                    @if ($isOwnProfile)
                        <button type="button" id="open-edit-profile-modal-btn"
                            class="follow-btn bg-gray-50 text-gray-900 hover:bg-gray-600 hover:text-white px-5 py-1.5 rounded-xl text-xs font-black transition-all active:scale-95 cursor-pointer uppercase tracking-tighter shadow-sm border border-gray-100">
                            Editar perfil
                        </button>
                    @else
                        <form
                            action="{{ $isFollowed ? route('user.unfollow', $profileUser['id']) : route('user.follow', $profileUser['id']) }}"
                            method="POST">
                            @csrf
                            <button type="submit"
                                class="follow-btn bg-gray-50 text-gray-900 hover:bg-gray-600 hover:text-white px-5 py-1.5 rounded-xl text-xs font-black transition-all active:scale-95 cursor-pointer uppercase tracking-tighter shadow-sm border border-gray-100">
                                {{ $isFollowed ? 'Seguindo' : 'Seguir' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="flex gap-8 mb-6">
                <div class="flex flex-col md:flex-row items-center gap-1">
                    <span class="font-black text-gray-900">{{ count($userPosts) }}</span>
                    <span class="text-gray-500 text-sm lowercase">publicações</span>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-1 cursor-pointer hover:opacity-70">
                    <span class="font-black text-gray-900">{{ $followersCount }}</span>
                    <span class="text-gray-500 text-sm lowercase">seguidores</span>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-1 cursor-pointer hover:opacity-70">
                    <span class="font-black text-gray-900">{{ $followingCount }}</span>
                    <span class="text-gray-500 text-sm lowercase">seguindo</span>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-100">
        <div class="flex justify-start gap-12">
            <div
                class="flex items-center gap-2 py-4 border-t border-gray-900 -mt-px text-xs font-black uppercase tracking-widest text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
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
