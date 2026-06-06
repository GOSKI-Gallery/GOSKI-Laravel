@extends('layouts.admin')

@section('title')
    Confirmar Exclusão
@endsection

@section('content')
<div class="w-full max-w-md">
    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold uppercase tracking-tight text-[var(--text-tertiary)] hover:text-[var(--text-primary)] mb-4 inline-block">← Voltar</a>

    <div class="bg-[var(--bg-card)] rounded-xl shadow-sm p-6 border border-[var(--border-color)]">
        <h2 class="text-xl font-bold text-red-600 mb-3">Deletar Usuário</h2>

        <p class="text-sm text-[var(--text-secondary)] mb-4">Esta ação é irreversível. Para confirmar, digite o username do usuário abaixo.</p>

        <div class="mb-4">
            <p class="text-[var(--text-primary)] mb-1"><strong>Usuário:</strong> {{ $user->name }}</p>
            <p class="text-[var(--text-secondary)] text-sm"><strong>Email:</strong> {{ $user->email }}</p>
        </div>

        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-bold text-[var(--text-primary)] mb-2">Digite o username para confirmar</label>
                <input type="text" name="username" placeholder="{{ $user->username }}"
                    class="w-full border border-[var(--border-color)] rounded-lg px-3 py-2 bg-[var(--bg-input)] text-[var(--text-primary)] placeholder:text-[var(--text-placeholder)] focus:outline-none focus:ring-2 focus:ring-red-500" required>
                @error('username')
                <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            @error('error')
            <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/30 rounded-lg text-red-600 text-sm">{{ $message }}</div>
            @enderror

            <div class="flex gap-3">
                <a href="{{ route('admin.users.index') }}" class="flex-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-[var(--text-primary)] font-bold py-2 px-4 rounded-lg text-center text-sm transition-all">Cancelar</a>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all cursor-pointer">Deletar Permanentemente</button>
            </div>
        </form>
    </div>
</div>
@endsection