<!DOCTYPE html>
<html lang="pt-br">

@include('shared.head')

<body class="min-h-screen bg-[var(--bg-primary)] text-[var(--text-primary)]">
    <x-header />

    @auth
        <x-create-post-modal />
        <x-profile.edit-profile-modal :user="Auth::user()" />
        <x-notification-modal :notifications="[]" />
    @endauth

    <div class="mx-auto flex flex-col justify-between px-4 py-4 max-w-6xl">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>