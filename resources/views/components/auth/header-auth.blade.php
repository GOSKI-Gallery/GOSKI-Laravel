<header class='flex justify-between items-center px-8 py-6'>
    <div class='flex items-center'>
        <a href='/' class='flex items-center group'> 
            <img class='w-12 h-12 transition-transform group-hover:scale-110' src="{{ asset('images/logo.svg') }}" alt="GoskiLogo">
            <h1 class='ml-3 text-3xl font-black tracking-tighter text-gray-900'>{{ config('app.name') }}</h1>
        </a>
    </div>

    @if(request()->is('/'))
        <a href="/register" 
           class='text-sm font-bold text-gray-500 hover:text-gray-600 border border-gray-200 px-4 py-2 rounded-xl transition-all hover:bg-white shadow-sm'>
            Crie sua conta
        </a>
    @elseif(request()->is('register'))
        <a href="/" 
           class='text-sm font-bold text-gray-500 hover:text-gray-600 border border-gray-200 px-4 py-2 rounded-xl transition-all hover:bg-white shadow-sm'>
            Faça seu login
        </a>
    @endif
</header>