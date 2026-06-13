@props(['title', 'value', 'href' => null, 'icon' => null])

@if ($href)
    <a href="{{ $href }}" class="bg-white dark:bg-zinc-950 border border-gray-100 dark:border-gray-700 rounded-xl p-6 transition-all hover:shadow-md hover:border-gray-200 dark:hover:border-gray-600 cursor-pointer group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ $title }}</p>
                <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white">{{ $value }}</p>
            </div>
            @if ($icon)
                <div class="text-gray-200 dark:text-gray-600 group-hover:text-gray-300 dark:group-hover:text-gray-500 transition-colors">
                    {{ $icon }}
                </div>
            @endif
        </div>
    </a>
@else
    <div class="bg-white dark:bg-zinc-950 border border-gray-100 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ $title }}</p>
                <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white">{{ $value }}</p>
            </div>
            @if ($icon)
                <div class="text-gray-200 dark:text-gray-600">
                    {{ $icon }}
                </div>
            @endif
        </div>
    </div>
@endif