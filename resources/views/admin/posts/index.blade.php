@extends('layouts.admin')

@section('title')
    Posts
@endsection

@section('content')
<div class="w-full">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-black uppercase tracking-tight text-gray-900 dark:text-white">Posts</h2>
        <span class="text-xs font-bold text-gray-400 dark:text-gray-500">Total: {{ $posts->total() }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($posts as $post)
            <x-admin.post-card :post="$post" />
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection