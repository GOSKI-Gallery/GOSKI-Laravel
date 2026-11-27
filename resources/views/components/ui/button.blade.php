@props([
    'type' => 'button',
    'label' => null,
    'variant' => 'primary', // primary, secondary, danger, ghost, outline
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'loading' => false,
    'class' => '',
    'href' => null,
])

<?php
$base = 'rounded-xl font-bold transition-all active:scale-[0.98] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary'   => 'bg-zinc-900 dark:bg-zinc-950 text-white shadow-lg shadow-zinc-200 dark:shadow-none hover:bg-zinc-600 hover:shadow-zinc-100',
    'secondary' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700',
    'danger'    => 'bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-900 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/50',
    'ghost'     => 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800',
    'outline'   => 'border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-900/50',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-5 py-3 text-sm',
    'lg' => 'px-8 py-3 text-base',
];

$variantClasses = $variants[$variant] ?? $variants['primary'];
$sizeClasses = $sizes[$size] ?? $sizes['md'];

$tag = $href ? 'a' : 'button';
$attrs = $href ? "href=\"{{ $href }}\"" : "type=\"{{ $type }}\"";
$disabledAttr = $disabled ? 'disabled' : '';
?>

<{{ $tag }} {{ $attrs }}
    {{ $disabledAttr }}
    {{ $loading ? 'disabled' : '' }}
    class="{{ $base }} {{ $variantClasses }} {{ $sizeClasses }} {{ $class }}">
    @if ($loading)
        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif
    {{ $label }}
</{{ $tag }}>
