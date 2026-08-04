@props(['posts'])

@foreach ($posts as $post)
    @if (!empty($post['users']))
        <article
            class="w-full mb-8 bg-white dark:bg-zinc-950 rounded-2xl p-4 shadow-sm">

            <div class="flex items-center justify-between px-5 mb-3">
                <a href="{{ route('profile.show', $post['users']['id']) }}" class="flex items-center">
                    <div class="w-10 h-10 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                        <img src="{{ $post['users']['profile_photo_url'] ?? '' }}"
                            alt="Profile"
                            class="w-full h-full object-cover"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <svg class="w-5 h-5 text-zinc-400 dark:text-zinc-500 hidden" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-zinc-900 dark:text-white font-bold text-lg">
                            {{ $post['users']['username'] }}
                        </p>
                        @if (!empty($post['latitude']) && !empty($post['longitude']))
                            <button type="button"
                                class="mt-0.5 flex items-center gap-1 text-blue-600 dark:text-blue-400 text-xs font-semibold cursor-pointer transition-colors hover:text-blue-700 dark:hover:text-blue-300"
                                data-location-post-id="{{ $post['id'] }}"
                                data-open-location>
                                <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/>
                                </svg>
                                <span>{{ $post['location_name'] ?: 'Ver no mapa' }}</span>
                            </button>
                        @endif
                        <p class="text-zinc-400 dark:text-zinc-500 text-xs">
                            {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                </a>

                @if (auth()->check() && auth()->id() !== $post['users']['id'])
                    <form action="{{ route('user.follow', $post['users']['id']) }}" method="POST" class="follow-form" data-follow-form>
                        @csrf
                        <button type="submit"
                            class="follow-btn bg-zinc-900 text-white px-6 py-2 rounded-lg text-sm font-bold transition-all active:scale-95 cursor-pointer"
                            data-user-id="{{ $post['users']['id'] }}"
                            data-following="{{ $post['is_followed_by_user'] ?? false ? '1' : '0' }}">
                            {{ ($post['is_followed_by_user'] ?? false) ? 'Seguindo' : 'Seguir' }}
                        </button>
                    </form>
                @endif
            </div>

            <div class="w-full aspect-square bg-zinc-900 dark:bg-zinc-800 overflow-hidden rounded-xl">
                <img src="{{ $post['image_url'] ?? '' }}" alt="Conteúdo do post"
                    class="w-full h-full object-cover">
            </div>

            <div class="flex items-center justify-between mt-3 px-2">
                <div class="mt-2">
                    <p class="text-zinc-800 dark:text-zinc-300 leading-5">
                        <a href="{{ route('profile.show', $post['users']['id']) }}"
                            class="font-bold text-zinc-900 dark:text-white hover:underline">{{ $post['users']['username'] }}</a>
                        <span class="text-zinc-500 dark:text-zinc-400">{{ $post['description'] }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <form action="{{ route('post.like.toggle', $post['id']) }}" method="POST" class="m-0 p-0 like-form">
                        @csrf
                            <button type="submit"
                                class="like-btn flex items-center gap-2 pr-3 py-2 rounded-xl active:bg-red-50 dark:active:bg-zinc-800 transition-all cursor-pointer"
                                data-post-id="{{ $post['id'] }}"
                                data-liked="{{ $post['is_liked_by_user'] ?? false ? '1' : '0' }}">
                                <svg class="w-6 h-6 transition-all like-icon text-zinc-900 dark:text-zinc-300" viewBox="0 0 24 24" fill="{{ ($post['is_liked_by_user'] ?? false) ? '#dc2626' : 'none' }}">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="{{ ($post['is_liked_by_user'] ?? false) ? 'none' : 'currentColor' }}" stroke-width="2"/>
                                </svg>
                                <span class="text-sm font-black {{ ($post['is_liked_by_user'] ?? false) ? 'text-red-600' : 'text-zinc-900 dark:text-zinc-300' }} transition-all like-count">{{ $post['likes_count'] ?? 0 }}</span>
                            </button>
                    </form>

                    <button type="button"
                        class="comment-btn flex items-center gap-2 pr-3 py-2 rounded-xl active:bg-zinc-100 dark:active:bg-zinc-800 transition-all cursor-pointer"
                        data-post-id="{{ $post['id'] }}"
                        data-open-comments>
                        <svg class="w-6 h-6 text-zinc-900 dark:text-zinc-300" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/>
                        </svg>
                        <span class="text-sm font-black text-zinc-900 dark:text-zinc-300 comment-count">{{ $post['comments_count'] ?? 0 }}</span>
                    </button>
                </div>
            </div>

            <x-feed.comments-drawer :postId="$post['id']"/>
        </article>
    @endif
@endforeach
