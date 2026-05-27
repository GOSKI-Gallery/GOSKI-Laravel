@extends('layouts.admin')

@section('content')
<div class="p-6">
    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">&larr; Voltar</a>
    
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Detalhes do Usuário</h1>
    
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
            <p class="text-lg text-gray-900">{{ $user->name }}</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <p class="text-lg text-gray-900">{{ $user->email }}</p>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Posts</p>
                <p class="text-2xl font-bold text-gray-900">{{ $user->posts_count }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Seguidores</p>
                <p class="text-2xl font-bold text-gray-900">{{ $user->followers_count }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Seguindo</p>
                <p class="text-2xl font-bold text-gray-900">{{ $user->following_count }}</p>
            </div>
        </div>

        <div class="border-t pt-6">
            <a href="{{ route('admin.users.remove', $user->id) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors">
                Deletar Usuário
            </a>
        </div>
    </div>
</div>
@endsection
