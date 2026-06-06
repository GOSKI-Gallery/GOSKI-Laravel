@props(['posts'])

<div class="mt-6">
    <h2 class="text-xs font-black uppercase tracking-wider text-[var(--text-tertiary)] mb-3">Publicações</h2>
    <div class="grid grid-cols-3 gap-2">
        @forelse($posts as $post)
            <div class="aspect-square bg-[var(--bg-skeleton)] rounded-lg overflow-hidden hover:opacity-80 transition-all cursor-pointer">
                <img class="w-full h-full object-cover" src="{{ $post->image_url }}" alt="Post">
            </div>
        @empty
            <p class="col-span-3 text-center text-[var(--text-tertiary)] py-8">Nenhuma publicação ainda.</p>
        @endforelse
    </div>
</div>