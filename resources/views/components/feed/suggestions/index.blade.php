<div class='flex flex-col gap-5 py-4'>
    <div class="flex items-center justify-between">
        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Sugestões para você</h3>
        <button class="text-[10px] font-bold text-gray-600 hover:text-gray-800 transition-colors">Gerado por IA</button>
    </div>

    {{-- Futuramente envolver este bloco em um @foreach($suggestions as $suggested) --}}
    <div class='flex flex-row justify-between items-center group'>
        <div class='flex justify-start items-center gap-3'>
            <div class="relative cursor-pointer">
                <img src='{{ asset('images/icons/icon.png') }}' 
                     alt='Profile Picture' 
                     class='rounded-full w-9 h-9 object-cover border border-gray-100 group-hover:border-gray-400 transition-all'>
            </div>

            <div>
                <h4 class='text-sm font-bold text-gray-900 tracking-tight leading-none'>@username_sugerido</h4>
            </div>
        </div>

        <button class="bg-gray-900 hover:bg-gray-600 text-white px-4 py-1.5 rounded-full font-black text-[10px] uppercase tracking-tighter transition-all active:scale-95 cursor-pointer shadow-sm">
            Seguir
        </button>
    </div>
</div>