@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Gestão de Usuários</h1>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-3 px-6 font-semibold text-gray-700">Nome</th>
                    <th class="text-left px-6 font-semibold text-gray-700">Email</th>
                    <th class="text-left px-6 font-semibold text-gray-700">Posts</th>
                    <th class="text-left px-6 font-semibold text-gray-700">Seguidores</th>
                    <th class="text-left px-6 font-semibold text-gray-700">Seguindo</th>
                    <th class="text-left px-6 font-semibold text-gray-700">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-6">{{ $user->name }}</td>
                    <td class="px-6">{{ $user->email }}</td>
                    <td class="px-6 text-center">{{ $user->posts_count }}</td>
                    <td class="px-6 text-center">{{ $user->followers_count }}</td>
                    <td class="px-6 text-center">{{ $user->following_count }}</td>
                    <td class="px-6">
                        <a href="{{ route('admin.users.detail', $user->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">Ver</a>
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
