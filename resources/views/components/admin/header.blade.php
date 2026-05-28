<header class="top-0 z-50 sticky backdrop-blur-lg border-[#D9D9D9] border-b">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div class='flex items-center'>
            <a href='/admin' class='flex items-center'> 
                <img class='w-12 h-12' src="{{ asset('images/logo.svg') }}" alt="GoskiLogo">
                <h1 class='ml-3 text-2xl font-semibold'>{{ env('APP_NAME') }}</h1>
            </a>
        </div>
    
        <div class='flex justify-center items-center py-3'>
            <h1 class='font-semibold text-xl text-gray-800'>@yield('title')</h1>
        </div>
    
        <div class="flex items-center gap-5"> 
            <form action="{{  route('logout') }}" method="POST" class="flex items-center gap-2 cursor-pointer">
                @csrf
                <button class="cursor-pointer">
                    <img class="w-8 h-8" src="images/icons/exit.png" alt="Sair">
                </button>
            </form>
        </div>
    </div>
</header> 
