@extends('layouts.admin')

@section('title')
    Painel
@endsection

@section('content')
<div class="w-full">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-xl shadow p-6 border hover:shadow-md hover:border-gray-400 transition-all cursor-pointer">
            <p class="text-sm text-gray-500">Usuários</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($totalUsers) }}</p>
        </a>

        <div class="bg-white rounded-xl shadow p-6 border">
            <p class="text-sm text-gray-500">Posts</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($totalPosts) }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border">
            <p class="text-sm text-gray-500">Moderação pendente</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $pendingPosts->count() }}</p>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow p-6 border">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Fila de Moderação</h2>
        @if($pendingPosts->isEmpty())
            <p class="text-sm text-gray-600">Nenhum post pendente de moderação.</p>
        @else
            <ul class="space-y-4">
                @foreach($pendingPosts as $post)
                    <li class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-4">
                            @if($post->image_url)
                                <div class="relative w-32 h-32 flex-shrink-0 overflow-hidden rounded-lg">
                                    <img src="{{ $post->image_url }}" alt="Post mídia" class="w-full h-full object-cover blur-xl">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                                        <span class="text-white text-xs font-bold px-2 py-1 bg-red-600 rounded">NSFW</span>
                                    </div>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <img src="{{ $post->users->avatar_url ?? asset('images/avatar-placeholder.png') }}" alt="" class="w-6 h-6 rounded-full object-cover">
                                    <p class="text-sm text-gray-600">{{ $post->users->name ?? '—' }}</p>
                                    <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-900 text-sm">{{ Str::limit($post->description ?? $post->content, 200) }}</p>

                                <div class="flex items-center gap-2 mt-3">
                                    <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST">
                                        @csrf
                                        <button class="px-4 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded-lg transition-colors">
                                            Aprovar
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-4 py-1.5 text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors">
                                            Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
