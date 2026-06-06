@props(['user', 'followersCount', 'followingCount'])

<div class="bg-[var(--bg-card)] rounded-2xl border border-[var(--border-color)] shadow-sm p-6">
    <div class="flex flex-col items-center">
        <div class="w-28 h-28 rounded-full bg-[var(--bg-avatar)] border-2 border-[var(--border-color)] overflow-hidden mb-4">
            <img class="w-full h-full object-cover"
                src="{{ $user->profile_photo_url ?? asset('images/icons/icon.png') }}"
                alt="{{ $user->username }}">
        </div>

        <h1 class="text-xl font-bold text-[var(--text-primary)]">{{ $user->name ?? $user->username }}</h1>
        <p class="text-sm font-light text-[var(--text-tertiary)]">@ {{ $user->username }}</p>

        <div class="flex items-center gap-8 mt-6">
            <div class="flex flex-col items-center gap-1">
                <span class="text-lg font-bold text-[var(--text-primary)]">{{ $userPostsCount ?? $user->posts_count ?? 0 }}</span>
                <span class="text-xs text-[var(--text-tertiary)]">Posts</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="text-lg font-bold text-[var(--text-primary)]">{{ $followersCount ?? 0 }}</span>
                <span class="text-xs text-[var(--text-tertiary)]">Seguidores</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="text-lg font-bold text-[var(--text-primary)]">{{ $followingCount ?? 0 }}</span>
                <span class="text-xs text-[var(--text-tertiary)]">Seguindo</span>
            </div>
        </div>

        @if(Auth::id() !== $user->id)
            <button type="button"
                class="follow-btn mt-6 bg-zinc-900 dark:bg-zinc-200 text-white dark:text-zinc-900 text-sm font-bold px-6 py-2 rounded-lg hover:opacity-80 transition-all cursor-pointer"
                data-user-id="{{ $user->id }}">
                Seguir
            </button>
        @else
            <button id="edit-profile-btn"
                class="mt-6 text-sm font-bold px-6 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 text-[var(--text-primary)] hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer">
                Editar Perfil
            </button>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('edit-profile-btn')?.addEventListener('click', () => {
        const modal = document.getElementById('edit-profile-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    });
</script>
@endpush