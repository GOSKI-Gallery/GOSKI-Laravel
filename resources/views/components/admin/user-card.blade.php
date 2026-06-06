@props(['user'])

<div class="flex items-center gap-3 bg-[var(--bg-card)] rounded-xl border border-[var(--border-color)] p-4 shadow-sm">
    <div class="w-12 h-12 rounded-full bg-[var(--bg-avatar)] overflow-hidden shrink-0">
        <img class="w-full h-full object-cover"
            src="{{ $user->profile_photo_url ?? asset('images/icons/icon.png') }}"
            alt="{{ $user->username }}">
    </div>
    <div class="flex-1 min-w-0">
        <p class="font-bold text-sm text-[var(--text-primary)] truncate">{{ $user->name ?? $user->username }}</p>
        <p class="text-xs text-[var(--text-tertiary)]">@ {{ $user->username }}</p>
    </div>
    <span class="text-xs text-[var(--text-tertiary)]">{{ $user->email }}</span>
</div>