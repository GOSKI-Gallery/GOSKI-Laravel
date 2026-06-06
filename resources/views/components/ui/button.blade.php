@props([
    'variant' => 'solid',
    'type' => 'submit',
    'disabled' => false,
    'loading' => false,
    'class' => '',
])

@php
    $baseClasses = 'inline-flex items-center justify-center h-14 rounded-xl font-bold text-lg transition-all duration-200 focus:outline-none';

    $variantClasses = match ($variant) {
        'solid' => 'bg-zinc-900 text-white border border-zinc-900 hover:opacity-80 active:opacity-80 disabled:opacity-50 dark:bg-zinc-200 dark:text-zinc-900 dark:border-zinc-200',
        'outline' => 'bg-transparent text-zinc-900 border border-zinc-900 hover:opacity-80 active:opacity-80 disabled:opacity-50 dark:text-zinc-200 dark:border-zinc-200',
        'follow' => 'bg-zinc-900 text-white text-sm font-bold px-6 py-2 rounded-lg',
        'menu' => 'py-3 px-2 text-lg font-bold w-full text-left',
        default => 'bg-zinc-900 text-white border border-zinc-900 hover:opacity-80 active:opacity-80 disabled:opacity-50 dark:bg-zinc-200 dark:text-zinc-900 dark:border-zinc-200',
    };
@endphp

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => "$baseClasses $variantClasses $class"]) }}
>
    @if ($loading)
        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 {{ $variant === 'outline' ? 'text-zinc-900 dark:text-zinc-200' : 'text-white dark:text-zinc-900' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif
    {{ $slot }}
</button>