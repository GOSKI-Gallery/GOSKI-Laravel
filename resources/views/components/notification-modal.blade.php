<div id="notification-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden mx-4" @click.stop>
        
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Notificações</h2>
            <button id="close-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="notification-content" class="max-h-[60vh] overflow-y-auto">
        </div>

    </div>
</div>

@push('scripts')
<script>
    if (!window.notificationModalLoaded) {
        window.notificationModalLoaded = true;

        const setupNotificationModal = () => {
            const notificationBtn = document.getElementById('notification-btn');
            const notificationModal = document.getElementById('notification-modal');
            if(!notificationModal) return;

            const closeModalBtn = document.getElementById('close-modal');
            const notificationContent = document.getElementById('notification-content');

            const formatDate = (dateString) => {
                const date = new Date(dateString);
                return new Intl.RelativeTimeFormat('pt-BR', { numeric: 'auto' }).format(
                    Math.round((date.getTime() - Date.now()) / (1000 * 60 * 60 * 24)),
                    'day'
                );
            };

            const loadNotifications = () => {
                notificationContent.innerHTML = '<div class="text-center text-gray-500 py-4">Carregando...</div>';
                
                fetch('{{ route("notifications.index") }}')
                    .then(response => response.json())
                    .then(data => {
                        notificationContent.innerHTML = '';
                        
                        if (data.length === 0) {
                            notificationContent.innerHTML = '<div class="text-center text-gray-500 py-4">Nenhuma notificação</div>';
                            return;
                        }

                        data.forEach(notification => {
                            const item = document.createElement('div');
                            item.className = `p-4 border-b border-gray-100 flex items-start gap-3 relative group transition-colors ${notification.is_read ? 'bg-white' : 'bg-blue-50/50'}`;
                            
                            const actionText = notification.type === 'like' ? 'curtiu sua publicação.' : 'começou a seguir você.';
                            const avatar = notification.profile_photo_url || `https://ui-avatars.com/api/?name=${notification.username}`;
                            const dot = !notification.is_read ? '<div class="w-2 h-2 bg-blue-500 rounded-full absolute top-4 right-4"></div>' : '';

                            item.innerHTML = `
                                <img src="${avatar}" class="w-10 h-10 rounded-full object-cover bg-gray-200">
                                <div class="flex-1 min-w-0 pr-6">
                                    <p class="text-sm text-gray-800">
                                        <span class="font-bold cursor-pointer hover:underline">${notification.username}</span>
                                        <span>${actionText}</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">${formatDate(notification.created_at)}</p>
                                </div>
                                <button class="delete-notification-btn opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-red-500 absolute top-3 right-8 transition-opacity" data-id="${notification.id}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                ${dot}
                            `;

                            notificationContent.appendChild(item);
                        });

                        // Add delete listeners
                        document.querySelectorAll('.delete-notification-btn').forEach(btn => {
                            btn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                const id = btn.getAttribute('data-id');
                                btn.closest('.p-4').remove();
                                
                                fetch(`/notifications/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                        'Accept': 'application/json'
                                    }
                                });
                            });
                        });
                    });
            };

            if (notificationBtn) {
                notificationBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    notificationModal.classList.remove('hidden');
                    notificationModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                    
                    loadNotifications();

                    fetch('{{ route("notifications.read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json'
                        }
                    });
                });
            }

            const closeModal = () => {
                notificationModal.classList.add('hidden');
                notificationModal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            };

            if(closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
            
            notificationModal.addEventListener('click', (e) => {
                if (e.target === notificationModal) closeModal();
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupNotificationModal);
        } else {
            setupNotificationModal();
        }
    }
</script>
@endpush