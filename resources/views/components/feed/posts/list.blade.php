@props(['posts'])

@foreach ($posts as $post)
    @if (!empty($post['users']))
        <article
            class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md">

            <div class="px-5 py-4 flex items-center justify-between bg-white/50 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <a href="{{ route('profile.show', $post['users']['id']) }}" class="relative group">
                        <img src="{{ $post['users']['profile_photo_url'] ?? asset('images/icons/icon.png') }}"
                            alt="Profile"
                            class="w-10 h-10 rounded-xl object-cover border-2 border-gray-50 group-hover:border-gray-400 transition-all">
                    </a>
                    <div>
                        <a href="{{ route('profile.show', $post['users']['id']) }}" class="font-bold text-gray-900 text-sm tracking-tight hover:underline">
                            {{ $post['users']['username'] }}
                        </a>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                            {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                </div>

                @if (auth()->check() && auth()->id() !== $post['users']['id'])
                    <form action="{{ route('user.follow', $post['users']['id']) }}" method="POST" class="follow-form" data-follow-form>
                        @csrf
                        <button type="submit"
                            class="follow-btn bg-gray-50 text-gray-900 hover:bg-gray-600 hover:text-white px-5 py-1.5 rounded-xl text-xs font-black transition-all active:scale-95 cursor-pointer uppercase tracking-tighter shadow-sm border border-gray-100"
                            data-user-id="{{ $post['users']['id'] }}"
                            data-following="{{ $post['is_followed_by_user'] ?? false ? '1' : '0' }}">
                            {{ ($post['is_followed_by_user'] ?? false) ? 'Seguindo' : 'Seguir' }}
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
                            <a href="{{ route('profile.show', $post['users']['id']) }}"
                                class="font-black text-gray-900 mr-2 uppercase tracking-tighter hover:underline">{{ $post['users']['username'] }}</a>
                            {{ $post['description'] }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('post.like.toggle', $post['id']) }}" method="POST" class="m-0 p-0 like-form">
                            @csrf
                            <button type="submit"
                                class="like-btn group flex items-center gap-2 pr-3 py-2 rounded-xl hover:bg-red-50 transition-all cursor-pointer"
                                data-post-id="{{ $post['id'] }}"
                                data-liked="{{ $post['is_liked_by_user'] ?? false ? '1' : '0' }}">
                                <img class="w-6 h-6 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all like-icon"
                                    src="{{ asset('images/icons/like.png') }}" alt="Like">
                                <span class="text-sm font-black text-gray-700 group-hover:text-red-600 like-count">{{ $post['likes_count'] ?? 0 }}</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </article>
    @endif
@endforeach
