@php
    $appName = config('app.name', 'GOSKI');
    $currentRoute = Route::currentRouteName();
@endphp

<header class="sticky top-0 z-40 w-full bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center hover:opacity-60 cursor-pointer">
                <a href="/feed" class="flex items-center group">
                    <img class="w-auto h-10 transition-transform" src="{{ asset('images/logo.svg') }}" alt="GoskiLogo">
                    <h1 class="ml-2 font-black text-2xl tracking-tighter text-gray-900">
                        {{ config('app.name') }}
                    </h1>
                </a>
            </div>

        <!-- Navigation -->
        <nav class="hidden md:flex items-center gap-0.5">
            <a href="{{ route('admin.dashboard') }}" 
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight {{ $currentRoute === 'admin.dashboard' ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }} rounded-lg transition-all">
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight {{ str_starts_with($currentRoute, 'admin.users') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }} rounded-lg transition-all">
                Usuários
            </a>
            <a href="{{ route('admin.posts.index') }}" 
               class="px-4 py-2 text-xs font-bold uppercase tracking-tight {{ str_starts_with($currentRoute, 'admin.posts') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }} rounded-lg transition-all">
                Posts
            </a>
        </nav>

        <!-- Logout -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="px-4 py-2 text-xs font-bold uppercase tracking-tight text-gray-500 border border-gray-100 rounded-lg hover:text-gray-900 hover:bg-gray-50 transition-all">
                Sair
            </button>
        </form>
    </div>
</header>