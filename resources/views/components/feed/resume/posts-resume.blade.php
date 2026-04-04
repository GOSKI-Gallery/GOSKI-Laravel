@props(['userPosts' => []])

<div class='grid grid-cols-3 gap-2'>
    @forelse ($userPosts as $post)
        <div class="aspect-square group relative overflow-hidden rounded-lg bg-gray-100">
            <img src="{{ $post['image_url'] ?? '' }}" 
                 alt="Post image"
                 class='w-full h-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75'>
            
            <div class="absolute inset-0 bg-indigo-900/10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
        </div>
    @empty
        <div class="col-span-3 py-4 text-center">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Nenhuma publicação</p>
        </div>
    @endforelse
</div>