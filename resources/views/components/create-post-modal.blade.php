<div id="create-post-modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-lg w-full">
        <h2 class="text-2xl font-bold mb-4">Criar Novo Post</h2>
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <textarea name="description" id="description" rows="3" class="w-full border rounded-lg px-3 py-2 resize-none" placeholder="Descrição" required></textarea>
            </div>
            <div class="mb-4">
                <div id="upload-area" class="relative flex justify-center items-center w-full h-64 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:border-gray-400 transition-all">
                    <div class="text-center" id="upload-placeholder">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-1 text-sm text-gray-600">
                            <span class="font-semibold">Clique para enviar</span> ou arraste e solte
                        </p>
                        <p class="text-xs text-gray-500">PNG, JPG or GIF</p>
                    </div>
                    <input type="file" name="image_url" id="image_url" class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer" required accept="image/png, image/jpeg, image/gif">
                    <img id="image-preview" class="hidden absolute top-0 left-0 h-full w-full object-contain rounded-lg" />
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" id="close-modal-btn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-2">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Postar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Este script é específico para este componente e já está incluído onde o componente é usado.
    if (!document.getElementById('create-post-modal-script-loaded')) {
        const scriptTag = document.createElement('script');
        scriptTag.id = 'create-post-modal-script-loaded';
        document.body.appendChild(scriptTag);

        const input = document.getElementById('image_url');
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');

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

        // Reseta o preview quando o modal for fechado
        const closeModalBtn = document.getElementById('close-modal-btn');
        closeModalBtn.addEventListener('click', () => {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            preview.src = '';
            input.value = '';
        });
    }
</script>
