@props(['comments' => []])

@forelse ($comments as $comment)
    <div class="flex gap-3" data-comment-id="{{ $comment['id'] }}">
        <div class="w-8 h-8 rounded-full overflow-hidden bg-zinc-200 dark:bg-zinc-700 flex-shrink-0">
            <img src="{{ $comment['users']['profile_photo_url'] ?? '' }}" alt=""
                class="w-full h-full object-cover"
                onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <svg class="w-4 h-4 text-zinc-400 hidden w-full h-full" viewBox="0 0 24 24" fill="none">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm">
                <a href="{{ route('profile.show', $comment['users']['id']) }}"
                    class="font-bold text-zinc-900 dark:text-white hover:underline">
                    {{ $comment['users']['username'] }}
                </a>
                <span class="text-zinc-600 dark:text-zinc-400">{{ $comment['body'] }}</span>
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
            </p>
        </div>
        @if (auth()->check() && auth()->id() === $comment['user_id'])
            <button type="button"
                class="text-zinc-400 hover:text-red-500 flex-shrink-0 cursor-pointer"
                data-delete-comment="{{ $comment['id'] }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        @endif
    </div>
@empty
    <p class="text-zinc-500 dark:text-zinc-400 text-sm text-center py-8">
        Nenhum comentário ainda. Seja o primeiro!
    </p>
@endforelse
