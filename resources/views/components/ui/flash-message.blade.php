<div class="fixed top-5 right-5 z-[100] flex flex-col gap-3">
    @if (session('success'))
        <div class="bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg border border-emerald-400 flex items-center gap-3 animate-bounce-short">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg border border-red-400 flex items-center gap-3 animate-shake">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
    @endif
</div>