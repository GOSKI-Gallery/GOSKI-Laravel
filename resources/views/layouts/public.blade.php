<!DOCTYPE html>
<html lang="pt-br">

@include('shared.head')

<body class='bg-[#FAFAFA] min-h-screen'>
    <x-header />

    <div class="flex flex-col justify-between mx-auto px-4 py-4 itens-center">
        @yield('content')
    </div>

</body>

</html>
