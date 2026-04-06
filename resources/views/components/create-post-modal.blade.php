<div id="create-post-modal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">

    <div
        class="w-full max-w-lg transform overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl transition-all">

        <div class="flex items-center justify-between border-b border-gray-50 bg-white px-6 py-4">
            <h2 class="text-xl font-bold tracking-tight text-gray-800">Criar nova publicação</h2>
            <button id="close-modal-x" class="cursor-pointer p-1 text-gray-400 transition-colors hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6">
            @csrf

            <div class="mb-5 border-2 border-gray-200 rounded-xl">
                <input name="description" id="description" rows="3"
                    class="w-full resize-none rounded-xl border-gray-200 px-4 py-3 text-sm outline-none transition-all placeholder:text-gray-300 focus:border-gray-500 focus:ring-4 focus:ring-gray-500/10"
                    placeholder="Descrição" required></input>
            </div>

            <div class="mb-6">
                <div id="upload-area"
                    class="group relative flex h-80 w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 transition-all hover:border-black/50 hover:bg-gray-100/30">

                    <div class="p-4 text-center transition-transform group-hover:scale-105" id="upload-placeholder">
                        <div
                            class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-sm">
                            <svg class="h-7 w-7 text-gray-900" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-600">Clique ou arraste o arquivo</p>
                        <p class="mt-1 text-[11px] uppercase tracking-tighter text-gray-400">PNG, JPG ou GIF até 10MB
                        </p>
                    </div>

                    <input type="file" name="image_url" id="image_url"
                        class="absolute inset-0 z-20 cursor-pointer opacity-0" required accept="image/*">

                    <img id="image-preview" class="absolute inset-0 z-10 hidden h-full w-full object-cover" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" id="close-modal-btn"
                    class="cursor-pointer px-6 py-2.5 text-sm font-bold text-gray-500 transition-colors hover:text-gray-800">
                    Cancelar
                </button>
                <button type="submit"
                    class="cursor-pointer rounded-xl bg-gray-900 px-10 py-2.5 text-sm font-bold text-white shadow-lg shadow-gray-100 transition-all hover:bg-black/30 active:scale-95">
                    Compartilhar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    if (!window.createPostModalLoaded) {
        window.createPostModalLoaded = true;

        const setupModal = () => {
            const modal = document.getElementById('create-post-modal');
            const openBtn = document.getElementById('open-modal-btn');
            const closeBtn = document.getElementById('close-modal-btn');
            const closeX = document.getElementById('close-modal-x');
            const input = document.getElementById('image_url');
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');
            const description = document.getElementById('description');

            if (!modal) return;

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
                }, 300);
            };

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (closeX) closeX.addEventListener('click', closeModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupModal);
        } else {
            setupModal();
        }
    }
</script>
