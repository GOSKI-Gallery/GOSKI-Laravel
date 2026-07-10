@props(['postId'])

<div class="fixed inset-0 z-50 hidden" id="comments-drawer-{{ $postId }}" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50 transition-opacity" data-comments-overlay></div>

    <div class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-zinc-950 shadow-xl transform transition-transform translate-x-full"
        data-comments-panel>
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Comentários</h2>
                <button type="button" class="p-2 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 cursor-pointer" data-comments-close>
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4" data-comments-list>
            </div>

            <div class="border-t border-zinc-200 dark:border-zinc-800 px-4 py-3">
                <form class="flex gap-2" data-comment-form>
                    @csrf
                    <input type="text" name="body" placeholder="Escreva um comentário..." maxlength="1000"
                        class="flex-1 px-3 py-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-400 border-0 focus:ring-2 focus:ring-zinc-300 dark:focus:ring-zinc-600 text-sm"
                        required>
                    <button type="submit"
                        class="px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-lg text-sm font-bold cursor-pointer disabled:opacity-50"
                        disabled>
                        Enviar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
