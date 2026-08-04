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

        <div id="location-map" class="h-96 w-full"></div>
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
            const closeBtn = document.getElementById('location-modal-close');
            let map = null;

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';

                if (map) {
                    map.remove();
                    map = null;
                }
                mapEl.innerHTML = '';
            };

            const openLocation = (postId) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                mapEl.innerHTML = '<p class="text-zinc-400 dark:text-zinc-500 text-sm text-center py-20">Carregando mapa...</p>';

                fetch('/posts/' + postId + '/location', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success || !data.post) {
                        mapEl.innerHTML = '<p class="text-zinc-400 dark:text-zinc-500 text-sm text-center py-20">Erro ao carregar localização.</p>';
                        return;
                    }
                    renderMap(data);
                })
                .catch(() => {
                    mapEl.innerHTML = '<p class="text-zinc-400 dark:text-zinc-500 text-sm text-center py-20">Erro ao carregar localização.</p>';
                });
            };

            const renderMap = (data) => {
                const post = data.post;
                const nearby = data.nearby || [];

                if (map) {
                    map.remove();
                }
                mapEl.innerHTML = '';

                if (!window.L) {
                    mapEl.innerHTML = '<p class="text-zinc-400 dark:text-zinc-500 text-sm text-center py-20">Mapa indisponível.</p>';
                    return;
                }

                map = window.L.map(mapEl).setView([post.latitude, post.longitude], 12);

                window.L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19,
                }).addTo(map);

                window.L.marker([post.latitude, post.longitude], { icon: mainIcon() })
                    .addTo(map)
                    .bindPopup(pinPopup(post));

                nearby.forEach(p => {
                    window.L.marker([p.latitude, p.longitude], { icon: cardIcon(p.image_url) })
                        .addTo(map)
                        .bindPopup(pinPopup(p));
                });
            };

            const mainIcon = () => window.L.divIcon({
                className: '',
                html: '<div class="w-10 h-10 rounded-full border-4 border-blue-600 dark:border-blue-400 bg-white dark:bg-zinc-900 flex items-center justify-center shadow-lg">' +
                    '<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor">' +
                    '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg></div>',
                iconSize: [40, 40],
                iconAnchor: [20, 40],
            });

            const cardIcon = (src) => window.L.divIcon({
                className: '',
                html: '<div class="w-14 h-14 rounded-lg overflow-hidden border-2 border-white dark:border-zinc-900 shadow-xl bg-zinc-200 dark:bg-zinc-800">' +
                    '<img src="' + src + '" class="w-full h-full object-cover" onerror="this.style.display=\'none\'"/></div>',
                iconSize: [56, 56],
                iconAnchor: [28, 56],
            });

            const pinPopup = (p) => {
                const username = (p.users && p.users.username) ? p.users.username : 'Usuário';
                const profileUrl = '{{ route('profile.show', ':userId') }}'.replace(':userId', p.user_id);
                const location = p.location_name ? p.location_name : '';
                const distance = p.distance_km !== undefined ? ' · ' + p.distance_km + ' km' : '';
                return '<div class="w-52">' +
                    '<img src="' + p.image_url + '" class="w-full h-32 object-cover rounded-lg mb-2" onerror="this.style.display=\'none\'"/>' +
                    '<a href="' + profileUrl + '" class="block font-bold text-zinc-900 dark:text-white text-sm hover:underline">' + username + '</a>' +
                    (location ? '<p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">' + location + distance + '</p>' : '') +
                    '</div>';
            };

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
