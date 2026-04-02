<!DOCTYPE html>
<html lang="pt-br">

    @include('shared.head')

    <body class='min-h-screen bg-[#FAFAFA]'>
        <x-header />
        <x-create-post-modal />

        <div class="itens-center mx-auto flex flex-col justify-between px-4 py-4">
            @yield('content')
        </div>

    </body>

</html>
