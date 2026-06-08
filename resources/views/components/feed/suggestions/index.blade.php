@props(['suggestedUsers' => []])

<div class='flex flex-col gap-5 py-4'>
    <div class="flex items-center justify-between">
        <h3 class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Sugestões para você</h3>
        <span class="text-[10px] font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#FF0000] via-[#AF054D] to-[#1B0EDB]">Recomendado</span>
    </div>

    @forelse ($suggestedUsers as $suggested)
        <div class='flex flex-row justify-between items-center group'>
            <a href='{{ route('profile.show', $suggested->id) }}' class='flex justify-start items-center gap-3'>
                <div class="w-9 h-9 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <img src='{{ $suggested->profile_photo_url ?? '' }}'
                         alt='Profile Picture'
                         class='w-full h-full object-cover'
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <svg class="w-4 h-4 text-zinc-400 dark:text-zinc-500 hidden" viewBox="0 0 24 24" fill="none">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                    </svg>
                </div>

                <div>
                    <h4 class='text-sm font-bold text-zinc-900 dark:text-white tracking-tight leading-none'>{{ $suggested->username }}</h4>
                </div>
            </a>

            <form action="{{ route('user.follow', $suggested->id) }}" method="POST" class="follow-form" data-follow-form>
                @csrf
                <button type="submit"
                    class="follow-btn bg-zinc-900 text-white px-6 py-2 rounded-lg font-black text-[10px] uppercase tracking-tighter transition-all active:scale-95 cursor-pointer shadow-sm"
                    data-user-id="{{ $suggested->id }}"
                    data-following="0">
                    Seguir
                </button>
            </form>
        </div>
    @empty
        <p class="text-xs text-zinc-400 dark:text-zinc-500 text-center py-6">Nenhuma sugestão no momento.</p>
    @endforelse
</div>
