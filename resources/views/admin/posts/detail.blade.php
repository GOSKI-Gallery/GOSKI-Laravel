@extends('layouts.admin')

@section('title')
    Post #{{ $post->id }}
@endsection

@section('content')
<div class="w-full max-w-4xl">
    <a href="{{ route('admin.posts.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">&larr; Voltar</a>

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <div class="bg-gray-100">
            <img src="{{ $post->image_url }}" alt="" class="w-full max-h-[60vh] object-contain mx-auto">
        </div>

        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ $post->users->avatar_url ?? asset('images/avatar-placeholder.png') }}" alt="" class="w-12 h-12 rounded-full object-cover">
                <div>
                    <p class="font-semibold text-gray-900">{{ $post->users->name ?? '—' }}</p>
                    <p class="text-sm text-gray-500">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if($post->description)
                <p class="text-gray-800 mb-6">{{ $post->description }}</p>
            @endif

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500">Curtidas</p>
                    <p class="text-xl font-bold text-gray-900">{{ $post->likes_count }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500">NSFW</p>
                    <p class="text-xl font-bold text-gray-900">{{ $post->is_nsfw ? 'Sim' : 'Não' }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500">Moderação</p>
                    <p class="text-xl font-bold text-gray-900">{{ $post->moderation_status }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-500">Post #</p>
                    <p class="text-xl font-bold text-gray-900">{{ $post->id }}</p>
                </div>
            </div>

            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este post?')">
                @csrf
                @method('DELETE')
                <button class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                    Deletar Post
                </button>
            </form>
        </div>
    </div>
</div>
@endsection