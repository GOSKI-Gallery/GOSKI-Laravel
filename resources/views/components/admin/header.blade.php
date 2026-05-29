<header class="top-0 z-50 sticky backdrop-blur-lg bg-white/80 border-[#D9D9D9] border-b">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="/admin" class="flex items-center">
                    <img class="w-10 h-10" src="{{ asset('images/logo.svg') }}" alt="GoskiLogo">
                    <h1 class="ml-3 text-xl font-semibold">{{ config('app.name') }}</h1>
                </a>

                <nav class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        Usuários
                    </a>
                    <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        Posts
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <span class="hidden sm:block text-sm font-medium text-gray-700">@yield('title')</span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer">
                        <img class="w-5 h-5" src="{{ asset('images/icons/exit.png') }}" alt="">
                        <span class="hidden sm:inline">Sair</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
