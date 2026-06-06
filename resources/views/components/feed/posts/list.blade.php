@props(['posts'])

@foreach ($posts as $post)
    <div class="bg-[var(--bg-card)] rounded-2xl border border-[var(--border-color)] shadow-sm overflow-hidden post-item">
        <div class="flex items-center justify-between px-5 py-3">
            <div class="flex items-center gap-3">
                <img class="w-10 h-10 rounded-full bg-[var(--bg-avatar)] border border-[var(--border-color)] object-cover"
                    src="{{ $post->users['profile_picture'] ?? $post->users->profile_photo_url ?? asset('images/icons/icon.png') }}"
                    alt="{{ $post->users['username'] ?? $post->users->username ?? 'Usuário' }}">
                <div>
                    <h1 class="text-lg font-bold text-[var(--text-primary)]">
                        {{ $post->users['username'] ?? $post->users->username ?? 'Usuário' }}
                    </h1>
                </div>
            </div>
        </div>

        <div class="aspect-square bg-[var(--bg-skeleton)] overflow-hidden">
            <img class="w-full h-full object-cover" src="{{ $post->image_url }}" alt="Post">
        </div>

        <div class="px-5 py-3">
            <div class="flex items-center gap-3 mb-2">
                <button type="button" class="like-btn cursor-pointer hover:opacity-80 transition-all"
                    data-post-id="{{ $post->id }}"
                    data-liked="{{ $post->is_liked_by_user ? 'true' : 'false' }}">
                    <div class="like-icon {{ $post->is_liked_by_user ? 'text-red-600' : 'text-[var(--icon-primary)] dark:text-zinc-300' }}">
                        @if($post->is_liked_by_user)
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.27 2 8.5C2 5.41 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5C22 12.27 18.6 15.36 13.45 20.03L12 21.35Z"/>
                            </svg>
                        @else
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16.5 3C14.76 3 13.09 3.81 12 5.08C10.91 3.81 9.24 3 7.5 3C4.42 3 2 5.41 2 8.5C2 12.27 5.4 15.36 10.55 20.03L12 21.35L13.45 20.03C18.6 15.36 22 12.27 22 8.5C22 5.41 19.58 3 16.5 3Z"/>
                            </svg>
                        @endif
                    </div>
                </button>
                <span class="like-count text-xs font-black text-[var(--text-primary)]">{{ $post->likes_count ?? 0 }}</span>

                @if((int) $post->user_id === (int) Auth::id())
                    <button type="button" class="delete-post-btn cursor-pointer hover:opacity-80 transition-all ml-auto text-[var(--icon-primary)]"
                        data-post-id="{{ $post->id }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 19C6 20.1 6.9 21 8 21H16C17.1 21 18 20.1 18 19V7H6V19ZM8 9H16V19H8V9ZM15.5 4L14.5 3H9.5L8.5 4H5V6H19V4H15.5Z"/>
                        </svg>
                    </button>
                @endif
            </div>

            <p class="text-sm text-[var(--text-secondary)]">
                <span class="font-bold text-[var(--text-primary)]">{{ $post->users['username'] ?? $post->users->username ?? 'Usuário' }}</span>
                {{ $post->description }}
            </p>
            <p class="text-xs text-[var(--text-tertiary)] mt-1">{{ $post->created_at->diffForHumans() }}</p>
        </div>
    </div>
@endforeach