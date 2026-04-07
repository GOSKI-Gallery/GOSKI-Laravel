@extends('layouts.public')

@section('content')
    <x-profile
        :profileUser="$profileUser"
        :userPosts="$userPosts"
        :followersCount="$followersCount"
        :followingCount="$followingCount"
        :isOwnProfile="$isOwnProfile ?? true"
        :isFollowed="$isFollowed ?? false"
    />

@endsection