@extends('layouts.admin')

@section('title')
    Usuários
@endsection

@section('content')
<div class="w-full">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-black uppercase tracking-tight text-[var(--text-primary)]">Usuários</h2>
        <span class="text-xs font-bold text-[var(--text-tertiary)]">Total: {{ $users->total() }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($users as $user)
            <x-admin.user-card :user="$user" />
        @endforeach
    </div>

    <div class="mt-8">
        {{ $users->links() }}
    </div>
</div>
@endsection