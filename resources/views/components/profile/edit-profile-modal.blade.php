@props(['user'])

<div id="edit-profile-modal"
    class="fixed inset-0 z-100 hidden items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">

    <div
        class="w-full max-w-lg transform overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl transition-all">

        <div class="flex items-center justify-between border-b border-gray-50 bg-white px-6 py-4">
            <h2 class="text-xl font-bold tracking-tight text-gray-800">Editar perfil</h2>
            <button id="close-edit-profile-modal-x" class="cursor-pointer p-1 text-gray-400 transition-colors hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6">
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
                    class="cursor-pointer px-6 py-2.5 text-sm font-bold text-gray-500 transition-colors hover:text-gray-800">
                    Cancelar
                </button>
                <button type="submit"
                    class="cursor-pointer rounded-xl bg-gray-900 px-10 py-2.5 text-sm font-bold text-white shadow-lg shadow-gray-100 transition-all hover:bg-black/30 active:scale-95">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>