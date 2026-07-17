@props(['postId'])

<div class="comments-section overflow-hidden transition-all duration-300 ease-in-out" id="comments-section-{{ $postId }}" style="max-height: 0">
    <div class="border-t border-zinc-200 dark:border-zinc-800 mt-3 pt-3 px-2">
        <div class="space-y-3 max-h-80 overflow-y-auto" data-comments-list>
            <p class="text-zinc-400 text-sm text-center py-4">Carregando...</p>
        </div>

        <form class="flex gap-2 mt-3 pb-1" data-comment-form>
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
