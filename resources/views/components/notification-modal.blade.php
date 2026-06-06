@props(['notifications' => []])

<div id="notification-modal" class="hidden fixed inset-0 z-[60] items-end justify-center bg-black/20">
    <div class="relative w-full max-w-lg bg-[var(--bg-modal)] rounded-t-[35px] p-6 shadow-2xl max-h-[80vh] overflow-y-auto">
        <div class="w-10 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full mx-auto mb-6"></div>

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-[var(--text-primary)]">Notificações</h2>
            <button onclick="document.getElementById('notification-modal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @forelse($notifications as $notification)
            <div class="flex items-start gap-3 py-4 border-b border-[var(--border-color)] last:border-b-0 {{ $notification->read_at ? '' : 'bg-blue-50/50 dark:bg-zinc-800' }}">
                <div class="w-2 h-2 rounded-full bg-blue-500 mt-2 shrink-0 {{ $notification->read_at ? 'opacity-0' : '' }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-base text-[var(--text-primary)]">{{ $notification->data['message'] ?? 'Nova notificação' }}</p>
                    <p class="text-xs text-[var(--text-tertiary)] mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                @if(!$notification->read_at)
                    <button class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline shrink-0 cursor-pointer"
                        onclick="markAsRead('{{ $notification->id }}')">
                        Marcar como lido
                    </button>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12 text-[var(--text-tertiary)]">
                <svg class="w-12 h-12 mb-3 opacity-50" viewBox="0 0 32 32" fill="currentColor">
                    <path d="M16 29.3333C17.4667 29.3333 18.6667 28.1333 18.6667 26.6667H13.3333C13.3333 28.1333 14.5333 29.3333 16 29.3333ZM24 21.3333V14.6667C24 10.6667 21.8667 7.33333 18 6.4V5.33333C18 4.22667 17.1067 3.33333 16 3.33333C14.8933 3.33333 14 4.22667 14 5.33333V6.4C10.1333 7.33333 8 10.6667 8 14.6667V21.3333L5.33333 24V25.3333H26.6667V24L24 21.3333Z"/>
                </svg>
                <p class="font-bold">Nenhuma notificação</p>
                <p class="text-sm">Você está em dia!</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('notification-btn')?.addEventListener('click', () => {
        const modal = document.getElementById('notification-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    });

    function markAsRead(id) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        }).then(() => location.reload());
    }
</script>
@endpush