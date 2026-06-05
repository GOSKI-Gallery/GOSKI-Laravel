@props(['posts'])

<div id="feed-posts" class="max-w-2xl mx-auto space-y-8 pb-12 gap-4">
    <x-feed.posts.list :posts="$posts" />
</div>

@if ($posts && count($posts) > 0)
    <div id="feed-sentinel" class="h-10"></div>
@else
    <div class="flex flex-col items-center justify-center py-32 text-center px-6">
        <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center mb-6">
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

    const sentinel = document.getElementById('feed-sentinel');
    if (sentinel) {
        let page = 1;
        let loading = false;

        const observer = new IntersectionObserver(async ([entry]) => {
            if (entry.isIntersecting && !loading) {
                loading = true;
                page++;
                try {
                    const html = await fetch(`/feed?page=${page}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(r => r.text());
                    if (html.trim()) {
                        document.getElementById('feed-posts').insertAdjacentHTML('beforeend', html);
                    } else {
                        observer.disconnect();
                    }
                } catch {
                    observer.disconnect();
                } finally {
                    loading = false;
                }
            }
        });
        observer.observe(sentinel);
    }
})();
</script>

<style>
.liked-active {
    filter: drop-shadow(0 0 4px rgba(220, 38, 38, 0.4));
    opacity: 1 !important;
}
</style>
