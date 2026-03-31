@extends('layouts.public')

@section('content')
    <div class="w-full">
        <div class="flex gap-6 space-x-6">
            <div class="w-3/12">
                <x-feed.suggestions />
            </div>
            <div class="w-6/12">
                <x-feed.posts :posts="$posts" />
            </div>
            <div class="w-3/12">
                <x-feed.resume />
            </div>
        </div>
    </div>
@endsection
