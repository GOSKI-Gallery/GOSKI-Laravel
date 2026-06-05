@props(['suggestedUsers' => []])

<div class='flex flex-col gap-5 py-4'>
    <div class="flex items-center justify-between">
        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Sugestões para você</h3>
        <span class="text-[10px] font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#FF0000] via-[#AF054D] to-[#1B0EDB]">Recomendado</span>
    </div>

    @forelse ($suggestedUsers as $suggested)
        <div class='flex flex-row justify-between items-center group'>
            <a href='{{ route('profile.show', $suggested->id) }}' class='flex justify-start items-center gap-3'>
                <div class="relative cursor-pointer">
                    <img src='{{ $suggested->profile_photo_url ?? asset('images/icons/icon.png') }}'
                         alt='Profile Picture'
                         class='rounded-xl w-9 h-9 object-cover border border-gray-100 group-hover:border-gray-400 transition-all'>
                </div>

                <div>
                    <h4 class='text-sm font-bold text-gray-900 tracking-tight leading-none'>{{ $suggested->username }}</h4>
                </div>
            </a>

            <form action="{{ route('user.follow', $suggested->id) }}" method="POST" class="follow-form" data-follow-form>
                @csrf
                <button type="submit"
                    class="follow-btn bg-gray-900 hover:bg-gray-600 text-white px-4 py-1.5 rounded-xl font-black text-[10px] uppercase tracking-tighter transition-all active:scale-95 cursor-pointer shadow-sm"
                    data-user-id="{{ $suggested->id }}"
                    data-following="0">
                    Seguir
                </button>
            </form>
        </div>
    @empty
        <p class="text-xs text-gray-400 text-center py-6">Nenhuma sugestão no momento.</p>
    @endforelse
</div>