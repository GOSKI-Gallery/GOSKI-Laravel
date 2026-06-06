@props(['suggestedUsers'])

@if($suggestedUsers->isNotEmpty())
    <div class="bg-[var(--bg-card)] rounded-2xl border border-[var(--border-color)] shadow-sm p-4">
        <h2 class="text-lg font-bold text-[var(--text-primary)] mb-4">Sugestões</h2>
        <div class="space-y-3">
            @foreach ($suggestedUsers as $suggested)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img class="w-10 h-10 rounded-full bg-[var(--bg-avatar)] border border-[var(--border-color)] object-cover"
                            src="{{ $suggested->profile_photo_url ?? asset('images/icons/icon.png') }}"
                            alt="{{ $suggested->username }}">
                        <div>
                            <h1 class="text-sm font-bold text-[var(--text-primary)]">{{ $suggested->username }}</h1>
                        </div>
                    </div>
                    <button type="button"
                        class="follow-btn bg-zinc-900 dark:bg-zinc-200 text-white dark:text-zinc-900 text-sm font-bold px-5 py-1.5 rounded-lg hover:opacity-80 transition-all cursor-pointer"
                        data-user-id="{{ $suggested->id }}">
                        Seguir
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endif

@push('scripts')
<script>
    document.querySelectorAll('.follow-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            this.textContent = '...';
            this.disabled = true;

            const followUrl = '{{ route("user.follow", "UID") }}'.replace('UID', userId);
            fetch(followUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({}),
            })
            .then(r => r.json())
            .then(data => {
                this.textContent = data.following ? 'Seguindo' : 'Seguir';
                this.classList.toggle('bg-zinc-900', data.following);
                this.classList.toggle('dark:bg-zinc-200', data.following);
                this.classList.toggle('bg-transparent', !data.following);
                this.classList.toggle('border', !data.following);
                this.classList.toggle('border-zinc-300', !data.following);
                this.classList.toggle('dark:border-zinc-600', !data.following);
                this.classList.toggle('text-zinc-900', !data.following);
                this.disabled = false;
            });
        });
    });
</script>
@endpush