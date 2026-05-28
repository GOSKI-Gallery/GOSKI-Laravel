@extends('layouts.admin')

@section('title')
    Confirmar Exclusão
@endsection

@section('content')
<div class="w-full max-w-md">
    <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">&larr; Voltar</a>

    <div class="bg-white rounded-lg shadow p-6 border">
        <h2 class="text-xl font-semibold text-red-600 mb-3">Deletar Usuário</h2>

        <p class="text-sm text-gray-600 mb-4">Esta ação é irreversível. Para confirmar, digite o username do usuário abaixo.</p>

        <div class="mb-4">
            <p class="text-gray-700 mb-1"><strong>Usuário:</strong> {{ $user->name }}</p>
            <p class="text-gray-700 text-sm"><strong>Email:</strong> {{ $user->email }}</p>
        </div>

        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Digite o username para confirmar</label>
                <input type="text" name="username" placeholder="{{ $user->username }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                @error('username')
                <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            @error('error')
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-600 text-sm">{{ $message }}</div>
            @enderror

            <div class="flex gap-3">
                <a href="{{ route('admin.users.index') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded text-center">Cancelar</a>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">Deletar Permanentemente</button>
            </div>
        </form>
    </div>
</div>
@endsection
