@php
    $currentRoute = 'admin.dashboard';
@endphp

@extends('layouts.admin')

@section('content')
<div class="w-full">
    <h1 class="text-2xl font-bold text-[var(--text-primary)] mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <x-admin.metric-card title="Total de Usuários" :value="$totalUsers ?? 0" />
        <x-admin.metric-card title="Total de Posts" :value="$totalPosts ?? 0" />
        <x-admin.metric-card title="Posts Pendentes" :value="$pendingPosts ?? 0" />
    </div>

    @if(isset($pendingModerations) && $pendingModerations->isNotEmpty())
        <h2 class="text-lg font-bold text-[var(--text-primary)] mb-4">Moderação Pendente</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($pendingModerations as $post)
                <x-admin.moderation-card :post="$post" />
            @endforeach
        </div>
    @endif
</div>
@endsection