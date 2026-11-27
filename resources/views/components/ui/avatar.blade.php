@props([
    'src' => null,
    'alt' => 'Avatar',
    'size' => 'w-10 h-10',
    'fallbackSize' => 'w-5 h-5',
    'class' => '',
    'ring' => true,
])

<?php
$hasSrc = !empty($src);
$fallbackSvg = '<svg class="w-full h-full text-zinc-400 dark:text-zinc-500" viewBox="0 0 24 24" fill="none">
    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
</svg>';
?>

<div class="relative {{ $class }}">
    @if ($hasSrc)
        <img src="{{ $src }}"
             alt="{{ $alt }}"
             class="{{ $size }} rounded-full object-cover {{ $ring ? 'border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800' : '' }}"
             loading="lazy"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
    @else
        <div class="{{ $size }} rounded-full {{ $ring ? 'border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800' : '' }} flex items-center justify-center">
            {{ $fallbackSvg }}
        </div>
    @endif

    @if ($hasSrc)
        <svg class="{{ $fallbackSize }} text-zinc-400 dark:text-zinc-500 absolute inset-0 flex items-center justify-center hidden" viewBox="0 0 24 24" fill="none">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
        </svg>
    @endif
</div>
