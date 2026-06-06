@extends('layouts.public')

@section('content')
<div class="flex flex-col lg:flex-row gap-6 mt-4">
    <div class="flex-1 max-w-2xl mx-auto w-full">
        <x-ui.flash-message />

        <div class="mb-6">
            <x-feed.resume.index :posts="$userPosts" />
        </div>

        <x-feed.posts.index
            :posts="$posts"
            :lastPage="$paginator->lastPage()"
            :currentPage="$paginator->currentPage()" />
    </div>

    <div class="hidden lg:block w-80 shrink-0">
        <div class="sticky top-20">
            <x-feed.suggestions.index :suggestedUsers="$suggestedUsers" />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('open-modal-btn')?.addEventListener('click', () => {
        document.getElementById('create-post-modal')?.classList.remove('hidden');
        document.getElementById('create-post-modal')?.classList.add('flex');
    });
</script>
@endpush