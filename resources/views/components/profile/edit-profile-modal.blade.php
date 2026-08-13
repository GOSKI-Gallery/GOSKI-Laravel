@props(['user'])

<div id="edit-profile-modal"
    class="fixed inset-0 z-100 hidden items-center justify-center bg-[var(--bg-overlay)] p-4 backdrop-blur-sm">

    <div
        class="w-full max-w-lg transform overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 bg-white shadow-2xl transition-all">

        <div class="flex items-center justify-between bg-white dark:bg-zinc-950 px-6 py-4">
            <h2 class="text-xl font-bold tracking-tight text-gray-500 dark:text-white">Editar perfil</h2>
            <button id="close-edit-profile-modal-x" class="cursor-pointer p-1 text-gray-400 transition-colors hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-zinc-950 p-6">
            @csrf
            @method('PUT')

            <x-auth.user-form 
                :user="$user" 
                :wrap-in-form="false" 
                :show-avatar="true" 
                :show-username="true" 
                :show-email="true" 
                :show-button="false"
                :show-password="true" 
                :show-password-confirmation="false"
            />

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" id="close-edit-profile-modal-btn"
                    class="cursor-pointer px-6 py-2.5 text-sm font-bold text-gray-500 transition-colors hover:text-zinc-900">
                    Cancelar
                </button>
                <button type="submit"
                    class="cursor-pointer rounded-xl bg-zinc-900 px-10 py-2.5 text-sm font-bold text-white shadow-lg shadow-zinc-100 dark:shadow-zinc-900 transition-all hover:bg-black/30 active:scale-95">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    if (!window.editProfileModalLoaded) {
        window.editProfileModalLoaded = true;

        const setupEditProfileModal = () => {
            const modal = document.getElementById('edit-profile-modal');
            if (!modal) return;
            
            const openBtn = document.getElementById('open-edit-profile-modal-btn');
            const closeBtn = document.getElementById('close-edit-profile-modal-btn');
            const closeX = document.getElementById('close-edit-profile-modal-x');

            if (openBtn) {
                openBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                });
            }

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            };

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (closeX) closeX.addEventListener('click', closeModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        };
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupEditProfileModal);
        } else {
            setupEditProfileModal();
        }
    }
</script>
@endpush