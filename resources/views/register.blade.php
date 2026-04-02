<!DOCTYPE html>
<html lang="pt-br">

    @include('shared.head')

    <body class='flex min-h-screen flex-col bg-[#ECECEC]'>

        <header class='flex items-center justify-between px-5 py-3'>
            <div class='flex items-center'>
                <a href='/' class='flex items-center'>
                    <img class='h-20 w-20' src="{{ asset('images/logo.svg') }}" alt="GoskiLogo">
                    <h1 class='ml-2 text-5xl'>{{ env('APP_NAME') }}</h1>
                </a>
            </div>

            <a href="/" class='text-2xl font-bold'>Faça seu login.</a>
        </header>

        <div class="flex justify-center">
            <div class="flex w-full max-w-fit flex-col items-start">
                <div class="flex flex-col items-start justify-center gap-3 py-24">
                    <div class="pb-8 text-start text-5xl font-normal leading-tight">
                        <h2>Crie sua conta e se</h2>
                        <p>
                            <span
                                class="bg-linear-to-r from-[#FF0000] via-[#AF054D] to-[#1B0EDB] bg-clip-text text-transparent">
                                expresse.
                            </span>
                        </p>
                    </div>

                    <h3 class="text-center text-xl font-bold">
                        Crie sua conta.
                    </h3>

                    <x-user-form />
                </div>
            </div>
        </div>
    </body>

</html>
