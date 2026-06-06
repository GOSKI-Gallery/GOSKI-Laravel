@extends('layouts.public')

@section('content')
<div class="max-w-2xl mx-auto w-full mt-4">
    <x-profile.index :user="$profileUser" :followersCount="$followersCount" :followingCount="$followingCount" />
</div>
@endsection