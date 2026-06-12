<!DOCTYPE html>
<html lang="pt-br">

    @include('shared.head')

    <body class='min-h-screen flex flex-col bg-[var(--bg-body)]'>
        <x-ui.flash-message />
        <x-header />

        @auth
            <x-create-post-modal />
            <x-profile.edit-profile-modal :user="Auth::user()" />
            <x-notification-modal :notifications="[]" />
        @endauth

        <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col px-4 py-4">
            @yield('content')
        </div>

        @stack('scripts')
    </body>
</html>
