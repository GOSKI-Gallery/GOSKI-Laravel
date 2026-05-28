@extends('layouts.admin')

@section('title')
    DASHBOARD.
@endsection

@section('title')
    Painel
@endsection

@section('content')
<div class="w-full">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow p-6 border">
            <p class="text-sm text-gray-500">Usuários</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($totalUsers) }}</p>
        </div>

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
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Posts pendentes</h2>
        @if($pendingPosts->isEmpty())
            <p class="text-sm text-gray-600">Nenhum post pendente no momento.</p>
        @else
            <ul class="space-y-3">
                @foreach($pendingPosts as $post)
                    <li class="p-3 border rounded hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-gray-600">{{ $post->users->name ?? '—' }}</p>
                                <p class="text-gray-900 font-medium">{{ Str::limit($post->content, 120) }}</p>
                            </div>
                            <div class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
