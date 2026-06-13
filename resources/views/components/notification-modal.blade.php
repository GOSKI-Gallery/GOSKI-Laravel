<div id="notification-modal" class="fixed inset-0 z-100 hidden items-center justify-center bg-[var(--bg-overlay)] backdrop-blur-sm transition-opacity">
    <div class="relative w-full max-w-md bg-[var(--bg-card)] dark:bg-zinc-950 rounded-xl shadow-2xl overflow-hidden mx-4" @click.stop>
        
        <div class="p-4 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-zinc-950">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 dark:text-white">Notificações</h2>
                <button id="mark-as-read-btn" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline hidden">Marcar todas como lidas</button>
            </div>
            <button id="close-modal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
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
            const markAsReadBtn = document.getElementById('mark-as-read-btn');
            const notificationContent = document.getElementById('notification-content');

            const loadNotifications = () => {
                notificationContent.innerHTML = '<div class="text-center text-gray-500 py-4">Carregando...</div>';
                
                fetch('{{ route("notifications.index") }}')
                    .then(response => {
                        if (!response.ok) throw new Error('Erro na requisição');
                        return response.json();
                    })
                    .then(data => {
                        notificationContent.innerHTML = '';
                        
                        if (data.length === 0) {
                            notificationContent.innerHTML = '<div class="text-center text-gray-500 py-4">Nenhuma notificação</div>';
                            markAsReadBtn.classList.add('hidden');
                            return;
                        }

                        const hasUnread = data.some(n => !n.is_read);
                        if(hasUnread) {
                            markAsReadBtn.classList.remove('hidden');
                        } else {
                            markAsReadBtn.classList.add('hidden');
                        }

                        data.forEach(notification => {
                            const item = document.createElement('div');
                            item.className = `notification-item p-4 border-b border-gray-100 dark:border-zinc-700 flex items-start gap-3 relative group transition-colors ${notification.is_read ? 'bg-white dark:bg-zinc-950' : 'bg-zinc-500 dark:bg-zinc-900'}`;
                            
                            const actionText = notification.type === 'like' ? 'curtiu sua publicação.' : 'começou a seguir você.';
                            const avatar = notification.profile_photo_url || "data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%239ca3af%22%3E%3Cpath d=%22M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z%22/%3E%3C/svg%3E";
                            
                            let dot = '';
                            if (!notification.is_read) {
                                dot = '<div class="notification-dot w-2 h-2 bg-blue-500 rounded-full absolute top-4 right-4"></div>';
                            }

                            const img = document.createElement('img');
                            img.src = avatar;
                            img.className = 'w-10 h-10 rounded-full object-cover bg-gray-200';

                            const usernameLink = document.createElement('a');
                            usernameLink.href = `/profile/${notification.user_id}`;
                            usernameLink.className = 'font-bold cursor-pointer hover:underline';
                            usernameLink.textContent = notification.username;

                            const actionSpan = document.createElement('span');
                            actionSpan.textContent = ' ' + actionText;

                            const textP = document.createElement('p');
                            textP.className = 'text-sm text-zinc-900 dark:text-zinc-100';
                            textP.appendChild(usernameLink);
                            textP.appendChild(actionSpan);

                            const timeP = document.createElement('p');
                            timeP.className = 'text-xs text-gray-500 mt-1';
                            timeP.textContent = notification.created_at_for_humans || '';

                            const textDiv = document.createElement('div');
                            textDiv.className = 'flex-1 min-w-0 pr-6';
                            textDiv.appendChild(textP);
                            textDiv.appendChild(timeP);

                            const closeBtn = document.createElement('button');
                            closeBtn.className = 'delete-notification-btn opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-red-500 absolute top-3 right-8 transition-opacity';
                            closeBtn.setAttribute('data-id', notification.id);
                            closeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

                            item.append(img, textDiv, closeBtn);

                            if (!notification.is_read) {
                                const dot = document.createElement('div');
                                dot.className = 'notification-dot w-2 h-2 bg-blue-500 rounded-full absolute top-4 right-4';
                                item.appendChild(dot);
                            }

                            notificationContent.appendChild(item);
                        });

                        document.querySelectorAll('.delete-notification-btn').forEach(btn => {
                            btn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                const id = btn.getAttribute('data-id');
                                btn.closest('.notification-item').remove();
                                
                                fetch(`/notifications/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                        'Accept': 'application/json'
                                    }
                                });
                            });
                        });
                    })
                    .catch(() => {
                        notificationContent.innerHTML = '<div class="text-center text-red-500 py-4">Erro ao carregar notificações.</div>';
                    });
            };

            if (notificationBtn) {
                notificationBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    notificationModal.classList.remove('hidden');
                    notificationModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                    loadNotifications();
                });
            }

            if (markAsReadBtn) {
                markAsReadBtn.addEventListener('click', () => {
                    fetch('{{ route("notifications.read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            document.querySelectorAll('.notification-item').forEach(item => {
                                item.classList.remove('bg-blue-50/50');
                                item.classList.add('bg-white');
                            });
                            document.querySelectorAll('.notification-dot').forEach(dot => dot.remove());
                            markAsReadBtn.classList.add('hidden');
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
