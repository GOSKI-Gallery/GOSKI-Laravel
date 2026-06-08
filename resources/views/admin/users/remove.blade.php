@extends('layouts.admin')

@section('title')
    Confirmar Exclusão
@endsection

@section('content')
<div class="w-full max-w-md">
    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold uppercase tracking-tight text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 mb-6 inline-block">&larr; Voltar</a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-black uppercase tracking-tight text-red-600 mb-3">Deletar Usuário</h2>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 font-bold">Esta ação é irreversível. Para confirmar, digite o username do usuário abaixo.</p>

        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700">
            <p class="text-gray-900 dark:text-white font-black uppercase tracking-tight">{{ $user->username }}</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm font-bold mt-1">{{ $user->email }}</p>
        </div>

        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-bold uppercase tracking-tight text-gray-700 dark:text-gray-300 mb-2">Digite o username para confirmar</label>
                <input type="text" name="username" placeholder="{{ $user->username }}" 
                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all placeholder:text-gray-300 dark:placeholder:text-gray-500 dark:text-gray-200" 
                    required>
                @error('username')
                <span class="text-red-600 text-sm font-bold mt-1 inline-block">{{ $message }}</span>
                @enderror
            </div>

            @error('error')
            <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-900 rounded-xl text-red-600 dark:text-red-400 text-sm font-bold">{{ $message }}</div>
            @enderror

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}" 
                   class="flex-1 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-black text-xs uppercase tracking-tight py-3 px-4 rounded-xl text-center transition-all cursor-pointer">
                    Cancelar
                </a>
                <button type="submit" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-tight py-3 px-4 rounded-xl transition-all active:scale-95">
                    Deletar Permanentemente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
