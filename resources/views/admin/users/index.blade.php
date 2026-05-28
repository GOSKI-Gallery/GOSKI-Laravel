@extends('layouts.admin')

@section('title')
    Usuários
@endsection

@section('content')
<div class="w-full">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <input type="search" placeholder="Buscar por nome ou email" class="border rounded-lg px-3 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500">Total: {{ $users->total() }}</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full table-auto">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-3 px-6 font-semibold text-gray-700">Usuário</th>
                    <th class="text-left px-6 font-semibold text-gray-700">Email</th>
                    <th class="text-center px-6 font-semibold text-gray-700">Posts</th>
                    <th class="text-center px-6 font-semibold text-gray-700">Seguidores</th>
                    <th class="text-center px-6 font-semibold text-gray-700">Seguindo</th>
                    <th class="text-center px-6 font-semibold text-gray-700">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-6 flex items-center gap-3">
                        <img src="{{ $user->avatar_url ?? asset('images/avatar-placeholder.png') }}" alt="avatar" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500">@{{ $user->username }}</div>
                        </div>
                    </td>
                    <td class="px-6 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 text-center">{{ $user->posts_count }}</td>
                    <td class="px-6 text-center">{{ $user->followers_count }}</td>
                    <td class="px-6 text-center">{{ $user->following_count }}</td>
                    <td class="px-6 text-center">
                        <a href="{{ route('admin.users.detail', $user->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
