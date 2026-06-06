<div id="create-post-modal" class="hidden fixed inset-0 z-[60] items-end justify-center bg-black/20">
    <div class="relative w-full max-w-lg bg-[var(--bg-modal)] rounded-t-[35px] p-6 shadow-2xl max-h-[80vh] overflow-y-auto">
        <div class="w-10 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full mx-auto mb-6"></div>

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-[var(--text-primary)]">Novo Post</h2>
            <button onclick="document.getElementById('create-post-modal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf

            <div class="flex items-center gap-3">
                <img class="w-10 h-10 rounded-full bg-[var(--bg-avatar)] border border-[var(--border-color)] object-cover"
                    src="{{ Auth::user()->profile_photo_url ?? asset('images/icons/icon.png') }}"
                    alt="{{ Auth::user()->username }}">
                <span class="font-bold text-[var(--text-primary)]">{{ Auth::user()->username }}</span>
            </div>

            <textarea name="description" rows="4" placeholder="O que você está compartilhando?"
                class="w-full p-4 rounded-2xl h-28 bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 text-zinc-800 dark:text-white placeholder:text-zinc-400 focus:outline-none resize-none"></textarea>

            <div class="flex flex-col items-center justify-center w-full h-40 rounded-3xl border-2 border-dashed border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-800/50 cursor-pointer hover:opacity-80 transition-all" onclick="document.getElementById('image-input').click()">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-zinc-400">
                    <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM13.96 12.29L11.21 15.83L9.25 13.47L6.5 17H17.5L13.96 12.29Z" fill="currentColor"/>
                </svg>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mt-2">Escolher foto</p>
                <input id="image-input" type="file" name="image_url" accept="image/*" required class="hidden" onchange="this.form.querySelector('label').textContent = this.files[0].name">
            </div>

            <button type="submit"
                class="w-full h-14 bg-zinc-900 dark:bg-zinc-200 text-white dark:text-zinc-900 font-bold text-lg rounded-xl border border-zinc-900 dark:border-zinc-200 hover:opacity-80 active:opacity-80 transition-all duration-200 cursor-pointer mt-2">
                Publicar
            </button>
        </form>
    </div>
</div>