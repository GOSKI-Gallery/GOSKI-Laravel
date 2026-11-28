@props([
    'type' => 'text',
    'name' => null,
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'icon' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'class' => '',
    'wrapperClass' => '',
    'rows' => null,
])

<?php
$oldValue = old($name) ?? $value;
$hasError = $error !== null;
$isTextarea = $type === 'textarea';
?>

<div class="{{ $wrapperClass }} flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $id ?? $name }}" class="text-xs font-bold uppercase tracking-tight text-zinc-500 dark:text-zinc-400">
            {{ $label }}
        </label>
    @endif

    <div class="relative group">
        @if ($icon && !$isTextarea)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-zinc-400 group-focus-within:text-zinc-600 dark:group-focus-within:text-zinc-300 transition-colors" viewBox="0 0 24 24" fill="none">
                    {{ $icon }}
                </svg>
            </div>
        @endif

        @if ($isTextarea)
            <textarea name="{{ $name }}"
                      id="{{ $id ?? $name }}"
                      placeholder="{{ $placeholder }}"
                      {{ $rows ? 'rows="' . $rows . '"' : '' }}
                      {{ $required ? 'required' : '' }}
                      {{ $disabled ? 'disabled' : '' }}
                      class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-[var(--bg-input)] dark:bg-zinc-900 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-zinc-400 focus:border-zinc-500 focus:ring-4 focus:ring-zinc-500/10 dark:text-zinc-200 {{ $class }} {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : '' }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }} {{ $icon ? 'pl-11' : 'pl-4' }}">{{ $oldValue }}</textarea>
        @else
            <input type="{{ $type }}"
                   name="{{ $name }}"
                   id="{{ $id ?? $name }}"
                   placeholder="{{ $placeholder }}"
                   value="{{ $oldValue }}"
                   {{ $required ? 'required' : '' }}
                   {{ $disabled ? 'disabled' : '' }}
                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-[var(--bg-input)] dark:bg-zinc-900 py-3 pl-11 pr-4 text-sm outline-none transition-all placeholder:text-zinc-400 focus:border-zinc-500 focus:ring-4 focus:ring-zinc-500/10 dark:text-zinc-200 {{ $class }} {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : '' }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
        @endif
    </div>

    @if ($hasError)
        <p class="ml-2 text-[10px] font-bold uppercase tracking-tight text-red-500">{{ $error }}</p>
    @endif
</div>
