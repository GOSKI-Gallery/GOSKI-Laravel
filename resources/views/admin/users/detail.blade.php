@extends('layouts.admin')

@section('title')
    {{ $user->username }}
@endsection

@section('content')
<div class="w-full max-w-4xl">
    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold uppercase tracking-tight text-gray-400 hover:text-gray-600 mb-6 inline-block">
        ← Voltar
    </a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Foto e Info Principal -->
        <div class="md:col-span-2 bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <div class="aspect-square bg-gray-50">
                <img src="{{ $user->profile_photo_url ?? asset('images/icons/icon.png') }}" 
                     alt="{{ $user->username }}"
                     class="w-full h-full object-cover">
            </div>

            <div class="p-6">
                <h2 class="text-2xl font-black uppercase tracking-tight text-gray-900">{{ $user->username }}</h2>
                <p class="text-sm text-gray-400 font-bold mt-1">{{ $user->email }}</p>

                <div class="grid grid-cols-3 gap-3 mt-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-400 font-bold">Posts</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $user->posts_count ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-400 font-bold">Seguidores</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $user->followers_count ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-400 font-bold">Seguindo</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $user->following_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações e Info Adicional -->
        <div class="space-y-6">
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Ações</p>
                <form action="{{ route('admin.users.remove', $user->id) }}" method="GET" class="w-full">
                    <button class="w-full py-3 text-xs font-black uppercase tracking-tight text-red-600 bg-red-50 border border-red-100 hover:bg-red-100 rounded-lg transition-colors">
                        Deletar Usuário
                    </button>
                </form>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Informações</p>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 font-bold">ID</p>
                        <p class="text-gray-900 font-mono text-xs mt-1 break-all">{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold">Membro desde</p>
                        <p class="text-gray-900 mt-1">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection