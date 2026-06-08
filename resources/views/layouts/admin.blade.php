<!DOCTYPE html>
<html lang="pt-br">

@include('shared.head')

<body class='flex flex-col bg-[var(--bg-body)] min-h-screen'>
    <x-admin.header />


    <x-ui.flash-message />

    <div class="flex flex-col mx-auto px-5 pt-20 pb-10 items-center justify-center w-full max-w-7xl">
        @yield('content')
    </div>

</body>

</html>
