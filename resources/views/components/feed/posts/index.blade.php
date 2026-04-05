@props(['posts'])

@if ($posts && count($posts) > 0)
    <div class="max-w-2xl mx-auto space-y-8 pb-12 gap-4">
        @foreach ($posts as $post)
            @if (!empty($post['users']))
                <article
                    class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md">

                    <div class="px-5 py-4 flex items-center justify-between bg-white/50 backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <div class="relative group cursor-pointer">
                                <img src="{{ $post['users']['profile_photo_url'] ?? asset('images/icons/icon.png') }}"
                                    alt="Profile"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-gray-50 group-hover:border-gray-400 transition-all">
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-900 text-sm tracking-tight">
                                    {{ $post['users']['username'] }}</h2>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                                    {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        @if (auth()->check() && auth()->id() !== $post['users']['id'])
                            <form action="{{-- route('user.follow', $post['users']['id']) --}}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-gray-50 text-gray-900 hover:bg-gray-600 hover:text-white px-5 py-1.5 rounded-full text-xs font-black transition-all active:scale-95 cursor-pointer uppercase tracking-tighter shadow-sm border border-gray-100">
                                    Seguir
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="relative aspect-square w-full bg-gray-50 border-y border-gray-50 overflow-hidden">
                        <img src="{{ $post['image_url'] ?? '' }}" alt="Conteúdo do post"
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-[1.02]">
                    </div>

                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-sm leading-snug">
                                <p class="text-gray-800">
                                    <span
                                        class="font-black text-gray-900 mr-2 uppercase tracking-tighter">{{ $post['users']['username'] }}</span>
                                    {{ $post['description'] }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    class="group flex items-center gap-2 pr-3 py-2 rounded-full hover:bg-red-50 transition-all cursor-pointer">
                                    <img class="w-6 h-6 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all"
                                        src="{{ asset('images/icons/like.png') }}" alt="Like">
                                    {{-- MOCK: No futuro, usar {{ count($post['likes']) }} --}}
                                    <span class="text-sm font-black text-gray-700 group-hover:text-red-600">42</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </article>
            @endif
        @endforeach
    </div>
@else
    <div class="flex flex-col items-center justify-center py-32 text-center px-6">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 tracking-tight">Nenhum post cadastrado.<h3>

                <button id="open-modal-btn-empty"
                    class="mt-8 bg-gray-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-gray-100 active:scale-95 transition-all cursor-pointer">
                    Fazer minha primeira postagem
                </button>
    </div>
@endif
