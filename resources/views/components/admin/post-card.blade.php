@props(['post'])

<div class="bg-[var(--bg-card)] rounded-xl border border-[var(--border-color)] overflow-hidden shadow-sm">
    <div class="flex items-center gap-3 px-4 py-3">
        <div class="w-10 h-10 rounded-full bg-[var(--bg-avatar)] overflow-hidden">
            <img class="w-full h-full object-cover"
                src="{{ $post->user->profile_photo_url ?? asset('images/icons/icon.png') }}"
                alt="{{ $post->user->username ?? 'User' }}">
        </div>
        <div>
            <p class="font-bold text-sm text-[var(--text-primary)]">{{ $post->user->username ?? 'Usuário' }}</p>
            <p class="text-xs text-[var(--text-tertiary)]">{{ $post->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <span class="ml-auto text-xs font-bold uppercase px-2 py-1 rounded-md {{ $post->moderation_status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : ($post->moderation_status === 'rejected' ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400') }}">
            {{ $post->moderation_status }}
        </span>
    </div>
    <div class="aspect-video bg-[var(--bg-skeleton)]">
        <img class="w-full h-full object-cover" src="{{ $post->image_url }}" alt="Post">
    </div>
    <div class="px-4 py-3">
        <p class="text-sm text-[var(--text-secondary)]">{{ $post->description }}</p>
    </div>
</div>