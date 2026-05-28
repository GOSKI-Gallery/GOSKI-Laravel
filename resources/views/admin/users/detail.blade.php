@extends('layouts.admin')

@section('title')
    Usuário — {{ $user->name }}
@endsection

@section('content')
<div class="w-full max-w-3xl">
    <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">&larr; Voltar</a>

    <div class="bg-white rounded-lg shadow p-6 flex gap-6">
        <div class="w-36">
            <img src="{{ $user->avatar_url ?? asset('images/avatar-placeholder.png') }}" alt="avatar" class="w-32 h-32 rounded-full object-cover">
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-semibold text-gray-900">{{ $user->name }}</h2>
            <p class="text-sm text-gray-600 mb-4">@{{ $user->username }} • {{ $user->email }}</p>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-gray-50 p-4 rounded text-center">
                    <p class="text-sm text-gray-600">Posts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->posts_count }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded text-center">
                    <p class="text-sm text-gray-600">Seguidores</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->followers_count }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded text-center">
                    <p class="text-sm text-gray-600">Seguindo</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->following_count }}</p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.users.remove', $user->id) }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">Deletar</a>
                <a href="#" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded">Editar</a>
            </div>
        </div>
    </div>
</div>
@endsection
