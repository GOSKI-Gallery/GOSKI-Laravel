@props(['class' => '', 'variant' => 'text'])

<div class="animate-pulse {{ $class }}">
    @if ($variant === 'card')
        <div class="space-y-3">
            <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4"></div>
            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2"></div>
            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-1/3"></div>
        </div>
    @elseif ($variant === 'post')
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700"></div>
                <div class="flex-1 space-y-1.5">
                    <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-1/4"></div>
                    <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/3"></div>
                </div>
            </div>
            <div class="aspect-video w-full bg-zinc-200 dark:bg-zinc-700 rounded-xl"></div>
            <div class="flex items-center gap-2">
                <div class="h-8 w-20 bg-zinc-200 dark:bg-zinc-700 rounded-full"></div>
                <div class="h-8 w-20 bg-zinc-200 dark:bg-zinc-700 rounded-full"></div>
                <div class="h-8 w-20 bg-zinc-200 dark:bg-zinc-700 rounded-full"></div>
            </div>
        </div>
    @elseif ($variant === 'avatar')
        <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700"></div>
    @elseif ($variant === 'image')
        <div class="aspect-video w-full bg-zinc-200 dark:bg-zinc-700 rounded-xl"></div>
    @elseif ($variant === 'map-tile')
        <div class="w-64 h-64 bg-zinc-200 dark:bg-zinc-700"></div>
    @elseif ($variant === 'map-pin')
        <div class="w-14 h-14 rounded-lg bg-zinc-200 dark:bg-zinc-700"></div>
    @else
        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded {{ $variant === 'circle' ? 'w-4' : 'w-full' }}"></div>
    @endif
</div>