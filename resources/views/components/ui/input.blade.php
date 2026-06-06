@props([
    'name' => '',
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'icon' => null,
    'rightIcon' => null,
])

<div class="flex-row items-center rounded-xl w-full h-14 px-4 bg-zinc-200 dark:bg-zinc-800 flex">
    @if ($icon)
        <div class="mr-3 opacity-30 text-[var(--icon-primary)]">
            {{ $icon }}
        </div>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'flex-1 bg-transparent text-black dark:text-white font-bold text-center focus:outline-none placeholder:text-zinc-400 dark:placeholder:text-zinc-400']) }}
    />

    @if ($rightIcon)
        <div class="w-5 h-5 ml-3 {{ isset($icon) ? 'opacity-0' : '' }}">
            {{ $rightIcon }}
        </div>
    @elseif ($icon)
        <div class="w-5 h-5 ml-3 opacity-0"></div>
    @endif
</div>

@error($name)
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
@enderror