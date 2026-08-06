<div id="location-modal"
    class="fixed inset-0 z-100 hidden items-center justify-center bg-[var(--bg-overlay)] p-4 backdrop-blur-sm">

    <div
        class="w-full max-w-2xl transform overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 bg-[var(--bg-card)] shadow-2xl transition-all">

        <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-700 bg-[var(--bg-surface)] px-6 py-4">
            <h2 class="text-sm font-black uppercase tracking-tight text-zinc-900 dark:text-white">Localização</h2>
            <button type="button" id="location-modal-close"
                class="cursor-pointer p-1 text-zinc-400 dark:text-zinc-500 transition-colors hover:text-zinc-600 dark:hover:text-zinc-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="bg-[var(--bg-surface)] p-4">
            <div id="location-map" class="relative w-full aspect-square overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                <p id="location-map-state" class="text-sm text-zinc-400 dark:text-zinc-500 text-center py-20">Carregando mapa...</p>
            </div>

            <div id="location-map-footer"
                class="mt-3 flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400">
                <svg class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/>
                </svg>
                <span id="location-map-name"></span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    if (!window.locationModalLoaded) {
        window.locationModalLoaded = true;

        const setupLocationModal = () => {
            const modal = document.getElementById('location-modal');
            if (!modal) return;

            const mapEl = document.getElementById('location-map');
            const stateEl = document.getElementById('location-map-state');
            const nameEl = document.getElementById('location-map-name');
            const closeBtn = document.getElementById('location-modal-close');

            const PIN_PATH = 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z';

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';
                mapEl.innerHTML = '';
                stateEl.style.display = 'block';
                stateEl.textContent = 'Carregando mapa...';
                nameEl.textContent = '';
            };

            const setState = (message) => {
                mapEl.innerHTML = '';
                stateEl.style.display = 'block';
                stateEl.textContent = message;
            };

            const openLocation = (postId) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';

                setState('Carregando mapa...');

                requestAnimationFrame(() => {
                    const rect = mapEl.getBoundingClientRect();
                    const width = Math.round(rect.width);
                    const height = Math.round(rect.height);
                    if (!width || !height) {
                        setState('Erro ao carregar localização.');
                        return;
                    }

                    fetch('/posts/' + postId + '/location?width=' + width + '&height=' + height, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success || !data.post || !data.map) {
                            setState('Erro ao carregar localização.');
                            return;
                        }
                        renderMap(data, width, height);
                    })
                    .catch(() => {
                        setState('Erro ao carregar localização.');
                    });
                });
            };

            const renderMap = (data, width, height) => {
                const post = data.post;
                const map = data.map;
                const pins = map.pins || [];
                const centerLeft = width / 2;
                const centerTop = height / 2;

                mapEl.innerHTML = '';
                stateEl.style.display = 'none';

                const tilesHtml = map.tiles.map(t =>
                    '<img src="' + t.url + '" alt="" loading="lazy" class="absolute w-64 h-64 max-w-none" style="left:' + t.left + 'px;top:' + t.top + 'px" onerror="this.style.display=\'none\'">'
                ).join('');

                const pinsHtml = pins.map(p => {
                    const safeSrc = String(p.image_url ?? '').replace(/[&<>"']/g, (ch) => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;',
                    }[ch]));
                    return '<div class="absolute w-14 h-14 rounded-lg overflow-hidden border-2 border-white dark:border-zinc-900 shadow-xl bg-zinc-200 dark:bg-zinc-800 pointer-events-none" style="left:' + (p.left - 28) + 'px;top:' + (p.top - 56) + 'px">' +
                        '<img src="' + safeSrc + '" alt="" class="w-full h-full object-cover" onerror="this.style.display=\'none\'"/></div>';
                }).join('');

                mapEl.innerHTML =
                    tilesHtml +
                    '<div class="absolute pointer-events-none" style="left:' + (centerLeft - 20) + 'px;top:' + (centerTop - 40) + 'px">' +
                        '<div class="w-10 h-10 rounded-full border-4 border-blue-600 dark:border-blue-400 bg-white dark:bg-zinc-900 flex items-center justify-center shadow-lg">' +
                            '<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor">' +
                                '<path d="' + PIN_PATH + '"/></svg></div></div>' +
                    pinsHtml +
                    '<div class="absolute bottom-1.5 left-1.5 px-1.5 py-0.5 rounded bg-white/80 dark:bg-zinc-950/70 text-[10px] leading-tight text-zinc-700 dark:text-zinc-300 pointer-events-none">' +
                        escapeHtml(map.attribution) +
                    '</div>';

                nameEl.textContent = post.location_name || 'Localização exata';
            };

            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (ch) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            }[ch]));

            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-open-location]');
                if (btn) {
                    e.preventDefault();
                    openLocation(btn.dataset.locationPostId);
                }
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupLocationModal);
        } else {
            setupLocationModal();
        }
    }
</script>
@endpush
