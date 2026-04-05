<div class='flex flex-col bg-white border border-gray-100 rounded-xl p-6 shadow-sm'>
    <div class='flex flex-row justify-between items-center mb-6'>
        <div class='flex justify-start items-center gap-3'>
            <img src='{{ Auth::user()->profile_photo_url ?? asset('images/icons/icon.png') }}' 
                 alt='Profile Picture'
                 class='rounded-full w-12 h-12 object-cover border-2 border-gray-50'>
            <div>
                <h2 class='text-lg font-black text-gray-900 tracking-tighter leading-none'>{{ Auth::user()->username }}</h2>
            </div>
        </div>
        <a href="/perfil" class="bg-gray-50 hover:bg-gray-100 text-gray-600 p-2 rounded-full transition-all group">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </a>
    </div>

    <div class='grid grid-cols-2 gap-4 py-4 border-y border-gray-50'>
        <div class="text-center border-r border-gray-50">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Seguidores</p>
            <h4 class="text-xl font-black text-gray-900">1.2k</h4> {{-- Mock: Futuramente count() --}}
        </div>
        <div class="text-center">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Seguindo</p>
            <h4 class="text-xl font-black text-gray-900">842</h4> {{-- Mock: Futuramente count() --}}
        </div>
    </div>

    <div class='mt-6'>
        <div class="flex items-center justify-between mb-4">
            <h4 class='text-[10px] font-black text-gray-400 uppercase tracking-widest'>Últimas publicações</h4>
            <span class="text-[10px] font-black text-gray-500 bg-gray-50 px-2 py-0.5 rounded">
                {{ count($userPosts) }}
            </span>
        </div>
        <x-feed.resume.posts-resume :userPosts="$userPosts" />
    </div>
</div>