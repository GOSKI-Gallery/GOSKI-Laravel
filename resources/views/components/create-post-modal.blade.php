<div id="create-post-modal"
    class="fixed inset-0 z-100 hidden items-center justify-center bg-[var(--bg-overlay)] p-4 backdrop-blur-sm">

    <div
        class="w-full max-w-lg transform overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 bg-[var(--bg-card)] shadow-2xl transition-all">

        <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-700 bg-[var(--bg-surface)] px-6 py-4">
            <h2 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Criar nova publicação</h2>
            <button id="close-modal-x" class="cursor-pointer p-1 text-gray-400 dark:text-gray-500 transition-colors hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="bg-[var(--bg-card)] p-6">
            @csrf

            <div class="mb-5 border-2 border-gray-200 dark:border-gray-700 rounded-xl">
                <input name="description" id="description" rows="3"
                    class="w-full resize-none rounded-xl border-gray-200 dark:border-gray-700 bg-[var(--bg-input)] dark:bg-zinc-900 px-4 py-3 text-sm outline-none transition-all placeholder:text-gray-300 dark:placeholder:text-gray-500 focus:border-gray-500 focus:ring-4 focus:ring-gray-500/10 dark:text-gray-200"
                    placeholder="Descrição" required></input>
            </div>

            <div class="mb-6">
                <div id="upload-area"
                    class="group relative flex h-80 w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-900 transition-all hover:border-black/50 hover:bg-gray-100/30">

                    <div class="p-4 text-center transition-transform group-hover:scale-105" id="upload-placeholder">
                        <div
                            class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-white dark:bg-zinc-900 shadow-sm">
                            <svg class="h-7 w-7 text-gray-900 dark:text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Clique ou arraste o arquivo</p>
                        <p class="mt-1 text-[11px] uppercase tracking-tighter text-gray-400 dark:text-gray-500">PNG, JPG ou GIF até 10MB
                        </p>
                    </div>

                    <input type="file" name="image_url" id="image_url"
                        class="absolute inset-0 z-20 cursor-pointer opacity-0" required accept="image/*">

                    <img id="image-preview" class="absolute inset-0 z-10 hidden h-full w-full object-cover" />
                </div>
            </div>

            <div class="mb-6">
                <button type="button" id="add-location-btn"
                    class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm font-bold text-zinc-700 dark:text-zinc-300 transition-all hover:border-blue-500/50 hover:text-blue-600 dark:hover:text-blue-400 active:scale-95">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/>
                    </svg>
                    <span id="add-location-label">Adicionar localização</span>
                </button>
                <p id="location-status" class="mt-2 text-xs font-semibold text-zinc-400 dark:text-zinc-500"></p>
                <input type="hidden" name="latitude" id="location-latitude">
                <input type="hidden" name="longitude" id="location-longitude">
                <input type="hidden" name="location_name" id="location-name">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" id="close-modal-btn"
                    class="cursor-pointer px-6 py-2.5 text-sm font-bold text-gray-500 dark:text-gray-400 transition-colors hover:text-zinc-900 dark:hover:text-white">
                    Cancelar
                </button>
                <button type="submit"
                    class="cursor-pointer rounded-xl bg-zinc-900 dark:bg-zinc-900 px-10 py-2.5 text-sm font-bold text-white shadow-lg shadow-gray-100 dark:shadow-none transition-all hover:bg-black/30 active:scale-95">
                    Compartilhar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    if (!window.createPostModalLoaded) {
        window.createPostModalLoaded = true;

        const setupCreatePostModal = () => {
            const modal = document.getElementById('create-post-modal');
            if (!modal) return;

            const openBtn = document.getElementById('open-modal-btn');
            const closeBtn = document.getElementById('close-modal-btn');
            const closeX = document.getElementById('close-modal-x');
            const input = document.getElementById('image_url');
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');
            const description = document.getElementById('description');
            const addLocationBtn = document.getElementById('add-location-btn');
            const locationStatus = document.getElementById('location-status');
            const locLat = document.getElementById('location-latitude');
            const locLng = document.getElementById('location-longitude');
            const locName = document.getElementById('location-name');
            const addLocationLabel = document.getElementById('add-location-label');

            const resetLocation = () => {
                if (locLat) locLat.value = '';
                if (locLng) locLng.value = '';
                if (locName) locName.value = '';
                if (locationStatus) {
                    locationStatus.textContent = '';
                    locationStatus.classList.remove('text-green-600', 'text-red-500', 'text-blue-600');
                }
                if (addLocationLabel) addLocationLabel.textContent = 'Adicionar localização';
            };

            if (addLocationBtn) {
                addLocationBtn.addEventListener('click', () => {
                    if (locLat && locLat.value) {
                        resetLocation();
                        return;
                    }

                    if (!navigator.geolocation) {
                        locationStatus.textContent = 'Geolocalização indisponível.';
                        locationStatus.classList.add('text-red-500');
                        return;
                    }

                    locationStatus.textContent = 'Obtendo localização...';
                    locationStatus.classList.remove('text-green-600', 'text-red-500');
                    locationStatus.classList.add('text-blue-600');

                    navigator.geolocation.getCurrentPosition(async (pos) => {
                        const lat = pos.coords.latitude.toFixed(7);
                        const lng = pos.coords.longitude.toFixed(7);
                        locLat.value = lat;
                        locLng.value = lng;
                        locationStatus.textContent = 'Resolvendo endereço...';

                        try {
                            const resp = await fetch(
                                'https://nominatim.openstreetmap.org/reverse?format=jsonv2&zoom=14&lat=' + lat + '&lon=' + lng,
                                { headers: { 'Accept': 'application/json' } }
                            );
                            const data = await resp.json();
                            locName.value = data.display_name ? data.display_name.substring(0, 255) : '';
                        } catch {
                            locName.value = '';
                        }

                        locationStatus.textContent = locName.value || 'Localização adicionada.';
                        locationStatus.classList.remove('text-blue-600');
                        locationStatus.classList.add('text-green-600');
                        addLocationLabel.textContent = 'Remover localização';
                    }, () => {
                        locationStatus.textContent = 'Não foi possível obter a localização.';
                        locationStatus.classList.remove('text-blue-600');
                        locationStatus.classList.add('text-red-500');
                    });
                });
            }

            if (openBtn) {
                openBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                });
            }

            if (input) {
                input.addEventListener('change', () => {
                    const file = input.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            placeholder.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';

                setTimeout(() => {
                    if (preview) preview.src = '';
                    if (preview) preview.classList.add('hidden');
                    if (placeholder) placeholder.classList.remove('hidden');
                    if (input) input.value = '';
                    if (description) description.value = '';
                    resetLocation();
                }, 300);
            };

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (closeX) closeX.addEventListener('click', closeModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupCreatePostModal);
        } else {
            setupCreatePostModal();
        }
    }
</script>
@endpush