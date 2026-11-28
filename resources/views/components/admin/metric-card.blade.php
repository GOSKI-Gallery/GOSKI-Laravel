@props(['title', 'value', 'href' => null, 'icon' => null])

@if ($href)
    <a href="{{ $href }}" class="bg-[var(--bg-card)] dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 transition-all hover:shadow-md hover:border-zinc-300 dark:hover:border-zinc-600 cursor-pointer group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">{{ $title }}</p>
                <p class="mt-3 text-3xl font-black text-zinc-900 dark:text-white">{{ $value }}</p>
            </div>
            @if ($icon)
                <div class="text-zinc-200 dark:text-zinc-600 group-hover:text-zinc-300 dark:group-hover:text-zinc-500 transition-colors">
                    {{ $icon }}
                </div>
            @endif
        </div>
    </a>
@else
    <div class="bg-[var(--bg-card)] dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">{{ $title }}</p>
                <p class="mt-3 text-3xl font-black text-zinc-900 dark:text-white">{{ $value }}</p>
            </div>
            @if ($icon)
                <div class="text-zinc-200 dark:text-zinc-600">
                    {{ $icon }}
                </div>
            @endif
        </div>
    </div>
@endif