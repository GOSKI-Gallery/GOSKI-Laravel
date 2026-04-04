<!DOCTYPE html>
<html lang="pt-br">

@include('shared.head')

<body class='flex min-h-screen flex-col bg-[#FAFAFA]'>

    <x-flash-message />

    <x-auth.header-auth />

    <div class="flex justify-center">
        <div class="flex w-full max-w-fit flex-col items-start">
            <div class="flex flex-col items-start justify-center gap-3 py-24">
                <div class="pb-6 font-black text-6xl text-start leading-[0.9] tracking-tighter text-gray-900">
                    <h2>Crie sua conta e se</h2>
                    <p>
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-[#FF0000] via-[#AF054D] to-[#1B0EDB]">
                            expresse.
                        </span>
                    </p>
                </div>

                <h3 class="text-center text-xl font-bold">
                    Crie sua conta.
                </h3>

                <x-auth.user-form />
            </div>
        </div>
    </div>
</body>

</html>
