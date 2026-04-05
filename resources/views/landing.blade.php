<!DOCTYPE html>
<html lang="pt-br">

@include('shared.head')

<body class='flex flex-col bg-[#FAFAFA] min-h-screen'>

    <x-flash-message />

    <x-auth.header-auth />

    <div class="flex justify-center">
        <div class="flex flex-col items-start w-full max-w-fit">
            <div class="flex flex-col justify-center items-start gap-3 py-24">
                <div class="pb-6 font-black text-6xl text-start leading-[0.9] tracking-tighter text-gray-900">
                    <h2>Acompanhe as</h2>
                    <p>
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-[#FF0000] via-[#AF054D] to-[#1B0EDB]">
                            expressões
                        </span>
                        <span>do</span>
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-[#FF0000] via-[#AF054D] to-[#1B0EDB]">
                            mundo.
                        </span>
                    </p>
                </div>

                <h3 class="font-bold text-xl text-center">
                    Faça seu login.
                </h3>

                <x-auth.login-form />
            </div>
        </div>
    </div>
</body>
