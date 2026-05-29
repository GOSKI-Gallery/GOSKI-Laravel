@props(['title', 'value', 'href' => null, 'icon' => null])

@if ($href)
    <a href="{{ $href }}" class="bg-white border border-gray-100 rounded-xl p-6 transition-all hover:shadow-md hover:border-gray-200 cursor-pointer group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $title }}</p>
                <p class="mt-3 text-3xl font-black text-gray-900">{{ $value }}</p>
            </div>
            @if ($icon)
                <div class="text-gray-200 group-hover:text-gray-300 transition-colors">
                    {{ $icon }}
                </div>
            @endif
        </div>
    </a>
@else
    <div class="bg-white border border-gray-100 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $title }}</p>
                <p class="mt-3 text-3xl font-black text-gray-900">{{ $value }}</p>
            </div>
            @if ($icon)
                <div class="text-gray-200">
                    {{ $icon }}
                </div>
            @endif
        </div>
    </div>
@endif