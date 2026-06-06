@props(['posts'])

<div class="grid grid-cols-3 gap-2">
    @foreach($posts as $post)
        <a href="{{ route('feed') }}" class="aspect-square bg-[var(--bg-skeleton)] rounded-lg overflow-hidden hover:opacity-80 transition-all">
            <img class="w-full h-full object-cover" src="{{ $post->image_url }}" alt="Post">
        </a>
    @endforeach
</div>