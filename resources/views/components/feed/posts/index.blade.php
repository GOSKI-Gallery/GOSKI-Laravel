@props(['posts'])

@if ($posts && count($posts) > 0)
    <div class="max-w-2xl mx-auto space-y-8 pb-12 gap-4">
        @foreach ($posts as $post)
            @if (!empty($post['users']))
                <article
                    class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md">

                    <div class="px-5 py-4 flex items-center justify-between bg-white/50 backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <div class="relative group cursor-pointer">
                                <img src="{{ $post['users']['profile_photo_url'] ?? asset('images/icons/icon.png') }}"
                                    alt="Profile"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-gray-50 group-hover:border-gray-400 transition-all">
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-900 text-sm tracking-tight">
                                    {{ $post['users']['username'] }}</h2>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                                    {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        @if (auth()->check() && auth()->id() !== $post['users']['id'])
                            <form action="{{ route('user.follow', $post['users']['id']) }}" method="POST" class="follow-form" data-follow-form>
                                @csrf
                                <button type="submit"
                                    class="follow-btn bg-gray-50 text-gray-900 hover:bg-gray-600 hover:text-white px-5 py-1.5 rounded-full text-xs font-black transition-all active:scale-95 cursor-pointer uppercase tracking-tighter shadow-sm border border-gray-100"
                                    data-user-id="{{ $post['users']['id'] }}"
                                    data-following="{{ $post['is_followed_by_user'] ?? false ? '1' : '0' }}">
                                    {{ ($post['is_followed_by_user'] ?? false) ? 'Seguindo' : 'Seguir' }}
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="relative aspect-square w-full bg-gray-50 border-y border-gray-50 overflow-hidden">
                        <img src="{{ $post['image_url'] ?? '' }}" alt="Conteúdo do post"
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-[1.02]">
                    </div>

                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-sm leading-snug">
                                <p class="text-gray-800">
                                    <span
                                        class="font-black text-gray-900 mr-2 uppercase tracking-tighter">{{ $post['users']['username'] }}</span>
                                    {{ $post['description'] }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('post.like.toggle', $post['id']) }}" method="POST" class="m-0 p-0 like-form">
                                    @csrf
                                    <button type="submit"
                                        class="like-btn group flex items-center gap-2 pr-3 py-2 rounded-full hover:bg-red-50 transition-all cursor-pointer"
                                        data-post-id="{{ $post['id'] }}"
                                        data-liked="{{ $post['is_liked_by_user'] ?? false ? '1' : '0' }}">
                                        <img class="w-6 h-6 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all like-icon"
                                            src="{{ asset('images/icons/like.png') }}" alt="Like">
                                        <span class="text-sm font-black text-gray-700 group-hover:text-red-600 like-count">{{ $post['likes_count'] ?? 0 }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </article>
            @endif
        @endforeach
    </div>
@else
    <div class="flex flex-col items-center justify-center py-32 text-center px-6">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 tracking-tight">Nenhum post cadastrado.<h3>

                <button id="open-modal-btn-empty"
                    class="mt-8 bg-gray-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-gray-100 active:scale-95 transition-all cursor-pointer">
                    Fazer minha primeira postagem
                </button>
    </div>
@endif

<script>
(function() {
    const csrf = '{{ csrf_token() }}';

    document.addEventListener('submit', function(e) {
        const form = e.target;

        if (form.classList.contains('like-form')) {
            e.preventDefault();
            const btn = form.querySelector('.like-btn');
            const icon = btn.querySelector('.like-icon');
            const countEl = btn.querySelector('.like-count');
            const postId = btn.dataset.postId;
            const wasLiked = btn.dataset.liked === '1';

            if (btn.disabled) return;
            btn.disabled = true;

            const newLiked = !wasLiked;
            let currentCount = parseInt(countEl.textContent);
            countEl.textContent = newLiked ? currentCount + 1 : Math.max(currentCount - 1, 0);
            btn.dataset.liked = newLiked ? '1' : '0';

            if (newLiked) {
                icon.classList.add('liked-active');
                countEl.classList.remove('text-gray-700');
                countEl.classList.add('text-red-600');
                icon.style.transform = 'scale(1.3)';
                icon.style.opacity = '1';
                setTimeout(() => { icon.style.transform = ''; }, 200);
            } else {
                icon.classList.remove('liked-active');
                countEl.classList.remove('text-red-600');
                countEl.classList.add('text-gray-700');
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({_token: csrf}),
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    btn.dataset.liked = wasLiked ? '1' : '0';
                    countEl.textContent = wasLiked ? Math.max(currentCount - 1, 0) : currentCount + 1;
                    if (wasLiked) {
                        icon.classList.add('liked-active');
                        countEl.classList.remove('text-gray-700');
                        countEl.classList.add('text-red-600');
                    } else {
                        icon.classList.remove('liked-active');
                        countEl.classList.remove('text-red-600');
                        countEl.classList.add('text-gray-700');
                    }
                }
            })
            .catch(() => {
                btn.dataset.liked = wasLiked ? '1' : '0';
                countEl.textContent = wasLiked ? Math.max(currentCount - 1, 0) : currentCount + 1;
                if (wasLiked) {
                    icon.classList.add('liked-active');
                    countEl.classList.remove('text-gray-700');
                    countEl.classList.add('text-red-600');
                } else {
                    icon.classList.remove('liked-active');
                    countEl.classList.remove('text-red-600');
                    countEl.classList.add('text-gray-700');
                }
            })
            .finally(() => {
                btn.disabled = false;
            });
        }

        if (form.classList.contains('follow-form')) {
            e.preventDefault();
            const btn = form.querySelector('.follow-btn');
            const userId = btn.dataset.userId;
            const isFollowing = btn.dataset.following === '1';

            if (btn.disabled) return;
            btn.disabled = true;

            const newFollowing = !isFollowing;
            btn.textContent = newFollowing ? 'Seguindo' : 'Seguir';
            btn.dataset.following = newFollowing ? '1' : '0';

            if (newFollowing) {
                btn.classList.add('bg-gray-600', 'text-white');
                btn.classList.remove('bg-gray-50', 'text-gray-900');
            } else {
                btn.classList.remove('bg-gray-600', 'text-white');
                btn.classList.add('bg-gray-50', 'text-gray-900');
            }

            const url = newFollowing
                ? '{{ route('user.follow', ':id') }}'.replace(':id', userId)
                : '{{ route('user.unfollow', ':id') }}'.replace(':id', userId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({_token: csrf}),
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    btn.dataset.following = isFollowing ? '1' : '0';
                    btn.textContent = isFollowing ? 'Seguindo' : 'Seguir';
                    if (isFollowing) {
                        btn.classList.add('bg-gray-600', 'text-white');
                        btn.classList.remove('bg-gray-50', 'text-gray-900');
                    } else {
                        btn.classList.remove('bg-gray-600', 'text-white');
                        btn.classList.add('bg-gray-50', 'text-gray-900');
                    }
                }
            })
            .catch(() => {
                btn.dataset.following = isFollowing ? '1' : '0';
                btn.textContent = isFollowing ? 'Seguindo' : 'Seguir';
                if (isFollowing) {
                    btn.classList.add('bg-gray-600', 'text-white');
                    btn.classList.remove('bg-gray-50', 'text-gray-900');
                } else {
                    btn.classList.remove('bg-gray-600', 'text-white');
                    btn.classList.add('bg-gray-50', 'text-gray-900');
                }
            })
            .finally(() => {
                btn.disabled = false;
            });
        }
    });
})();
</script>

<style>
.liked-active {
    filter: drop-shadow(0 0 4px rgba(220, 38, 38, 0.4));
    opacity: 1 !important;
}
</style>
