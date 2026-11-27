@props([
    'id' => 'modal',
    'title' => null,
    'closeLabel' => 'Fechar',
    'class' => '',
    'overlayClass' => '',
    'contentClass' => '',
    'showCloseButton' => true,
    'closable' => true,
])

<div id="{{ $id }}"
     class="fixed inset-0 z-100 hidden items-center justify-center bg-[var(--bg-overlay)] p-4 backdrop-blur-sm transition-opacity {{ $overlayClass }}"
     {{ $closable ? 'data-modal' : '' }}>

    <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 bg-[var(--bg-card)] shadow-2xl transition-all {{ $contentClass }}">

        @if ($title || $showCloseButton)
            <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-700 bg-[var(--bg-surface)] px-6 py-4">
                @if ($title)
                    <h2 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h2>
                @endif

                @if ($showCloseButton)
                    <button id="close-{{ $id }}"
                            class="cursor-pointer p-1 text-gray-400 dark:text-gray-500 transition-colors hover:text-gray-600 dark:hover:text-gray-300"
                            aria-label="{{ $closeLabel }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        @endif

        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    if (!window.modalLoaded_{{ $id }}) {
        window.modalLoaded_{{ $id }} = true;

        const modal_{{ $id }} = document.getElementById('{{ $id }}');
        const closeBtn_{{ $id }} = document.getElementById('close-{{ $id }}');

        window.openModal_{{ $id }} = () => {
            modal_{{ $id }}.classList.remove('hidden');
            modal_{{ $id }}.classList.add('flex');
            document.body.style.overflow = 'hidden';
        };

        const closeModal_{{ $id }} = () => {
            modal_{{ $id }}.classList.add('hidden');
            modal_{{ $id }}.classList.remove('flex');
            document.body.style.overflow = 'auto';
        };

        @if ($showCloseButton)
        <script>
            closeBtn_{{ $id }}.addEventListener('click', closeModal_{{ $id }});
        </script>
        @endif
        @if ($closable)
        <script>
            modal_{{ $id }}.addEventListener('click', function(e) {
                if (e.target === modal_{{ $id }}) closeModal_{{ $id }}();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal_{{ $id }}.classList.contains('hidden')) {
                    closeModal_{{ $id }}();
                }
            });
        </script>
        @endif
    }
</script>
@endpush
