<!DOCTYPE html>
<html lang="pt-br">

    @include('shared.head')

    <body class='min-h-screen bg-[var(--bg-body)]'>
        <x-ui.flash-message />
        <x-header />

        @auth
            <x-create-post-modal />
            <x-profile.edit-profile-modal :user="Auth::user()" />
            <x-notification-modal :notifications="[]" />
        @endauth

        <div class="itens-center mx-auto flex flex-col justify-between px-4 py-4">
            @yield('content')
        </div>

        @stack('scripts')
    </body>
</html>
