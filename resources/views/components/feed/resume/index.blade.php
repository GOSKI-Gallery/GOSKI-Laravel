@props(['userPosts' => [], 'followersCount' => 0, 'followingCount' => 0])

<div class='flex flex-col bg-white dark:bg-zinc-950 rounded-2xl p-6 shadow-sm'>
    <div class='flex flex-row justify-between items-center mb-6'>
        <a href="{{ route('profile') }}" class='flex justify-start items-center gap-3'>
            <div class="w-12 h-12 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <img src='{{ Auth::user()->profile_photo_url ?? '' }}' alt='Profile Picture'
                    class='w-full h-full object-cover'
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <svg class="w-5 h-5 text-zinc-400 dark:text-zinc-500 hidden" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                </svg>
            </div>
            <div>
                <h2 class='text-lg font-black text-zinc-900 dark:text-white tracking-tighter leading-none'>{{ Auth::user()->username }}
                </h2>
            </div>
        </a>
        <a href="{{ route('profile') }}" class="bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 p-2 rounded-xl transition-all group">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
            </svg>
        </a>
    </div>

    <div class='grid grid-cols-2 gap-4 py-4 border-y border-zinc-100 dark:border-zinc-800'>
        <div class="text-center border-r border-zinc-100 dark:border-zinc-800">
            <p class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Seguidores</p>
            <h4 class="text-xl font-black text-zinc-900 dark:text-white">{{ $followersCount }}</h4>
        </div>
        <div class="text-center">
            <p class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-1">Seguindo</p>
            <h4 class="text-xl font-black text-zinc-900 dark:text-white">{{ $followingCount }}</h4>
        </div>
    </div>

    <div class='mt-6'>
        <div class="flex items-center justify-between mb-4">
            <h4 class='text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest'>Últimas publicações</h4>
            <span class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                {{ count($userPosts) }}
            </span>
        </div>
        <x-feed.resume.posts-resume :userPosts="$userPosts" />
    </div>
</div>
