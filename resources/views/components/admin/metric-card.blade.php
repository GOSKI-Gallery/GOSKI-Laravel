@props(['title', 'value', 'icon' => null])

<div class="bg-[var(--bg-card)] rounded-xl border border-[var(--border-color)] p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-[var(--text-tertiary)]">{{ $title }}</p>
            <p class="text-3xl font-bold text-[var(--text-primary)] mt-1">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="text-[var(--icon-primary)] opacity-50">
                {{ $icon }}
            </div>
        @endif
    </div>
</div>