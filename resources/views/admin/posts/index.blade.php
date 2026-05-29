@extends('layouts.admin')

@section('title')
    Posts
@endsection

@section('content')
<div class="w-full">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Todos os Posts</h2>
        <span class="text-sm text-gray-500">Total: {{ $posts->total() }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
            <div class="bg-white rounded-xl shadow border overflow-hidden group">
                <a href="{{ route('admin.posts.detail', $post->id) }}" class="block">
                    <div class="aspect-square overflow-hidden bg-gray-100">
                        <img src="{{ $post->image_url }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ $post->users->avatar_url ?? asset('images/avatar-placeholder.png') }}" alt="" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-sm font-medium text-gray-900">{{ $post->users->name ?? '—' }}</span>
                    </div>

                    <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $post->description }}</p>

                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>{{ $post->created_at->format('d/m/Y H:i') }}</span>
                        <span>{{ $post->likes_count }} curtidas</span>
                    </div>

                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Tem certeza que deseja excluir este post?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                            Deletar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection