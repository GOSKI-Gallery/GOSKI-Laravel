@props(['user'])

<div id="edit-profile-modal" class="hidden fixed inset-0 z-[60] items-end justify-center bg-black/20">
    <div class="relative w-full max-w-lg bg-[var(--bg-modal)] rounded-t-[35px] p-6 shadow-2xl max-h-[85vh] overflow-y-auto">
        <div class="w-10 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full mx-auto mb-6"></div>

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-[var(--text-primary)]">Editar Perfil</h2>
            <button onclick="document.getElementById('edit-profile-modal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <x-auth.user-form
            :action="route('profile.update')"
            method="PUT"
            :user="$user" />
    </div>
</div>