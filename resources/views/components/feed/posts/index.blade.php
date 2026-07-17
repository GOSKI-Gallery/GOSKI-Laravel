@props(['posts'])

<div id="feed-posts" class="max-w-2xl mx-auto space-y-8 pb-12 gap-4">
    <x-feed.posts.list :posts="$posts" />
</div>

@if ($posts && count($posts) > 0)
    <div id="feed-sentinel" class="h-10"></div>
@else
    <div class="flex flex-col items-center justify-center py-32 text-center px-6">
        <div class="w-20 h-20 bg-gray-100 dark:bg-zinc-950 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 dark:text-gray-200 tracking-tight">Nenhum post cadastrado.<h3>

                <button id="open-modal-btn-empty"
                    class="mt-8 bg-gray-600 dark:bg-zinc-900 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-gray-100 dark:shadow-none active:scale-95 transition-all cursor-pointer">
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
                icon.setAttribute('fill', '#dc2626');
                icon.querySelector('path').setAttribute('stroke', 'none');
                countEl.classList.remove('text-zinc-900', 'dark:text-zinc-300');
                countEl.classList.add('text-red-600');
                icon.style.transform = 'scale(1.3)';
                setTimeout(() => { icon.style.transform = ''; }, 200);
            } else {
                icon.setAttribute('fill', 'none');
                icon.querySelector('path').setAttribute('stroke', 'currentColor');
                countEl.classList.remove('text-red-600');
                countEl.classList.add('text-zinc-900', 'dark:text-zinc-300');
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
                if (data.success) {
                    countEl.textContent = data.likes_count;
                    btn.dataset.liked = data.liked ? '1' : '0';
                    if (data.liked) {
                        icon.setAttribute('fill', '#dc2626');
                        icon.querySelector('path').setAttribute('stroke', 'none');
                        countEl.classList.remove('text-zinc-900', 'dark:text-zinc-300');
                        countEl.classList.add('text-red-600');
                    } else {
                        icon.setAttribute('fill', 'none');
                        icon.querySelector('path').setAttribute('stroke', 'currentColor');
                        countEl.classList.remove('text-red-600');
                        countEl.classList.add('text-zinc-900', 'dark:text-zinc-300');
                    }
                } else {
                    btn.dataset.liked = wasLiked ? '1' : '0';
                    countEl.textContent = currentCount;
                    if (wasLiked) {
                        icon.setAttribute('fill', '#dc2626');
                        icon.querySelector('path').setAttribute('stroke', 'none');
                        countEl.classList.remove('text-zinc-900', 'dark:text-zinc-300');
                        countEl.classList.add('text-red-600');
                    } else {
                        icon.setAttribute('fill', 'none');
                        icon.querySelector('path').setAttribute('stroke', 'currentColor');
                        countEl.classList.remove('text-red-600');
                        countEl.classList.add('text-zinc-900', 'dark:text-zinc-300');
                    }
                }
            })
            .catch(() => {
                btn.dataset.liked = wasLiked ? '1' : '0';
                countEl.textContent = currentCount;
                if (wasLiked) {
                    icon.setAttribute('fill', '#dc2626');
                    icon.querySelector('path').setAttribute('stroke', 'none');
                    countEl.classList.remove('text-zinc-900', 'dark:text-zinc-300');
                    countEl.classList.add('text-red-600');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.querySelector('path').setAttribute('stroke', 'currentColor');
                    countEl.classList.remove('text-red-600');
                    countEl.classList.add('text-zinc-900', 'dark:text-zinc-300');
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
                }
            })
            .catch(() => {
                btn.dataset.following = isFollowing ? '1' : '0';
                btn.textContent = isFollowing ? 'Seguindo' : 'Seguir';
            })
            .finally(() => {
                btn.disabled = false;
            });
        }
    });

    function toggleComments(postId) {
        const section = document.getElementById('comments-section-' + postId);
        if (!section) return;

        const isOpen = section.style.maxHeight !== '0px';

        document.querySelectorAll('.comments-section').forEach(el => {
            if (el.id !== 'comments-section-' + postId && el.style.maxHeight !== '0px') {
                el.style.maxHeight = '0px';
            }
        });

        if (isOpen) {
            section.style.maxHeight = '0px';
            return;
        }

        section.style.maxHeight = section.scrollHeight + 'px';

        loadCommentsInline(postId);
    }

    function loadCommentsInline(postId) {
        const section = document.getElementById('comments-section-' + postId);
        if (!section) return;
        const list = section.querySelector('[data-comments-list]');
        list.innerHTML = '<p class="text-zinc-400 text-sm text-center py-4">Carregando...</p>';

        fetch('/posts/' + postId + '/comments', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.success && Array.isArray(data.comments)) {
                if (data.comments.length === 0) {
                    list.innerHTML = '<p class="text-zinc-500 dark:text-zinc-400 text-sm text-center py-4">Nenhum comentário ainda. Seja o primeiro!</p>';
                } else {
                    list.innerHTML = data.comments.map(c => renderComment(postId, c)).join('');
                }
            } else {
                list.innerHTML = '<p class="text-zinc-400 text-sm text-center py-4">Erro ao carregar comentários.</p>';
            }

            if (section.style.maxHeight !== '0px') {
                requestAnimationFrame(() => {
                    section.style.maxHeight = section.scrollHeight + 'px';
                });
            }
        })
        .catch(() => {
            list.innerHTML = '<p class="text-zinc-400 text-sm text-center py-4">Erro ao carregar comentários.</p>';
            if (section.style.maxHeight !== '0px') {
                requestAnimationFrame(() => {
                    section.style.maxHeight = section.scrollHeight + 'px';
                });
            }
        });
    }

    function renderComment(postId, c) {
        const avatar = '<img src="' + (c.users ? c.users.profile_photo_url || '' : '') + '" alt="" class="w-full h-full object-cover" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">' +
            '<svg class="w-full h-full text-zinc-400 hidden" viewBox="0 0 24 24" fill="none"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/></svg>';
        const username = c.users ? c.users.username : 'Usuário';
        const profileUrl = '{{ route('profile.show', ':userId') }}'.replace(':userId', c.user_id);
        const time = c.time_ago || '';
        const deleteBtn = (c.user_id === '{{ auth()->id() }}')
            ? '<button type="button" class="text-zinc-400 hover:text-red-500 flex-shrink-0 cursor-pointer" data-delete-comment="' + c.id + '" data-post-id="' + postId + '">' +
                '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                '<path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>' +
                '</svg></button>'
            : '';
        return '<div class="flex gap-3" data-comment-id="' + c.id + '">' +
            '<div class="w-8 h-8 rounded-full overflow-hidden bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 flex items-center justify-center">' + avatar + '</div>' +
            '<div class="flex-1 min-w-0">' +
                '<p class="text-sm"><a href="' + profileUrl + '" class="font-bold text-zinc-900 dark:text-white hover:underline">' + username + '</a> ' +
                '<span class="text-zinc-600 dark:text-zinc-400">' + escapeHtml(c.body) + '</span></p>' +
                '<p class="text-xs text-zinc-400 mt-1">' + time + '</p>' +
            '</div>' +
            deleteBtn +
        '</div>';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    document.addEventListener('click', function(e) {
        const openBtn = e.target.closest('[data-open-comments]');
        if (openBtn) {
            e.preventDefault();
            toggleComments(openBtn.dataset.postId);
            return;
        }

        const deleteBtn = e.target.closest('[data-delete-comment]');
        if (deleteBtn) {
            e.preventDefault();
            const commentId = deleteBtn.dataset.deleteComment;
            const postId = deleteBtn.dataset.postId;
            if (!commentId) return;
            if (deleteBtn.disabled) return;
            deleteBtn.disabled = true;

            fetch('/posts/comments/' + commentId, {
                method: 'DELETE',
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
                if (data.success) {
                    const item = deleteBtn.closest('[data-comment-id]');
                    if (item) item.remove();
                    const list = deleteBtn.closest('[data-comments-list]');
                    if (list && !list.querySelector('[data-comment-id]')) {
                        list.innerHTML = '<p class="text-zinc-500 dark:text-zinc-400 text-sm text-center py-4">Nenhum comentário ainda. Seja o primeiro!</p>';

                    }
                    updateCommentCount(postId, -1);
                }
            })
            .catch(() => {})
            .finally(() => {
                deleteBtn.disabled = false;
            });
            return;
        }
    });

    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.hasAttribute('data-comment-form')) {
            e.preventDefault();
            const input = form.querySelector('[name="body"]');
            const body = input.value.trim();
            if (!body) return;
            const btn = form.querySelector('button[type="submit"]');
            if (btn.disabled) return;
            btn.disabled = true;

            const section = form.closest('[id^="comments-section-"]');
            const postId = section ? section.id.replace('comments-section-', '') : '';

            fetch('/posts/' + postId + '/comments', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({_token: csrf, body: body}),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    input.value = '';
                    loadCommentsInline(postId);
                    updateCommentCount(postId, 0, data.comments_count);
                }
            })
            .catch(() => {})
            .finally(() => {
                btn.disabled = false;
            });
        }
    });

    function updateCommentCount(postId, delta, absolute) {
        const btns = document.querySelectorAll('[data-open-comments][data-post-id="' + postId + '"]');
        btns.forEach(btn => {
            const countEl = btn.querySelector('.comment-count');
            if (countEl) {
                if (absolute !== undefined) {
                    countEl.textContent = absolute;
                } else {
                    let c = parseInt(countEl.textContent) + delta;
                    countEl.textContent = Math.max(c, 0);
                }
            }
        });
    }

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
