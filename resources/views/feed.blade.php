@extends('layouts.public')

@section('content')
    <div class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-3">
                <x-feed.suggestions :suggestedUsers="$suggestedUsers" />
            </div>
            <div class="md:col-span-6">
                <x-feed.posts :posts="$posts" />
            </div>
            <div class="md:col-span-3">
                <x-feed.resume :userPosts="$userPosts" :followersCount="$followersCount" :followingCount="$followingCount" />
            </div>
        </div>
    </div>
@endsection
