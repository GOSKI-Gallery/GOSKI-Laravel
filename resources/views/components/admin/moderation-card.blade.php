@props(['post'])

<div class="bg-[var(--bg-card)] rounded-xl border border-[var(--border-color)] overflow-hidden shadow-sm">
    <div class="px-4 py-3 border-b border-[var(--border-color)]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[var(--bg-avatar)] overflow-hidden">
                <img class="w-full h-full object-cover"
                    src="{{ $post->user->profile_photo_url ?? asset('images/icons/icon.png') }}"
                    alt="{{ $post->user->username ?? 'User' }}">
            </div>
            <div>
                <p class="font-bold text-sm text-[var(--text-primary)]">{{ $post->user->username ?? 'Usuário' }}</p>
                <p class="text-xs text-[var(--text-tertiary)]">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
    @if($post->image_url)
        <div class="aspect-video bg-[var(--bg-skeleton)]">
            <img class="w-full h-full object-cover" src="{{ $post->image_url }}" alt="Post">
        </div>
    @endif
    <div class="px-4 py-3">
        <p class="text-sm text-[var(--text-secondary)] mb-3">{{ $post->description }}</p>
        <div class="flex gap-2">
            <form action="{{ route('admin.posts.moderate', $post->id) }}" method="POST">
                @csrf
                <input type="hidden" name="moderation_status" value="approved">
                <button class="px-4 py-2 text-xs font-bold uppercase bg-emerald-500 text-white rounded-lg hover:opacity-80 transition-all cursor-pointer">Aprovar</button>
            </form>
            <form action="{{ route('admin.posts.moderate', $post->id) }}" method="POST">
                @csrf
                <input type="hidden" name="moderation_status" value="rejected">
                <button class="px-4 py-2 text-xs font-bold uppercase bg-red-500 text-white rounded-lg hover:opacity-80 transition-all cursor-pointer">Rejeitar</button>
            </form>
        </div>
    </div>
</div>