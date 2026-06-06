@props([
    'show' => false,
    'title' => '',
    'maxWidth' => 'max-w-lg',
])

<div
    x-data="{ open: @js($show) }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[60] flex items-end justify-center"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
>
    <div class="fixed inset-0 bg-black/20" x-on:click="open = false"></div>

    <div class="relative w-full {{ $maxWidth }} bg-white dark:bg-zinc-900 rounded-t-[35px] p-6 shadow-2xl max-h-[85vh] overflow-y-auto">
        <div class="w-10 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full mx-auto mb-6"></div>

        @if ($title)
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $title }}</h2>
                <button @click="open = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush