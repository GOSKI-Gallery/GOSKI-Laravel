@extends('layouts.admin')

@section('content')
<div class="p-6">
    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">&larr; Voltar</a>
    
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow p-6 border-2 border-red-200">
            <h2 class="text-2xl font-bold text-red-600 mb-4">Deletar Usuário</h2>
            
            <div class="mb-6 p-4 bg-red-50 rounded border border-red-200">
                <p class="text-red-700"><strong>Aviso:</strong> Esta ação é irreversível e deletará permanentemente o usuário e todos seus dados.</p>
            </div>
            
            <div class="mb-6">
                <p class="text-gray-700 mb-2"><strong>Usuário:</strong> {{ $user->name }}</p>
                <p class="text-gray-700"><strong>Email:</strong> {{ $user->email }}</p>
            </div>
            
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Digite o username para confirmar:
                    </label>
                    <input type="text" name="username" placeholder="{{ $user->username }}" 
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
                        required>
                    @error('username')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                @error('error')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-600 text-sm">
                    {{ $message }}
                </div>
                @enderror
                
                <div class="flex gap-3">
                    <a href="{{ route('admin.users.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors">
                        Deletar Permanentemente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
