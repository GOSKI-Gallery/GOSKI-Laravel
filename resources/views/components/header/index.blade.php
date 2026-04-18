<header class="top-0 z-50 sticky bg-white border-gray-200 border-b">
    <div class="mx-auto px-4 max-w-6xl">
        <div class="flex justify-between items-center h-16"> 
            
            <div class="flex items-center hover:opacity-60 cursor-pointer">
                <a href="/feed" class="flex items-center group">
                    <img class="w-auto h-10 transition-transform" src="{{ asset('images/logo.svg') }}" alt="GoskiLogo">
                    <h1 class="ml-2 font-black text-2xl tracking-tighter text-gray-900">
                        {{ config('app.name') }}
                    </h1>
                </a>
            </div>
            
            <div class="flex items-center gap-6">
                <button id="open-modal-btn" class="hover:opacity-60 transition-all cursor-pointer">
                    <img class="w-7 h-7" src="{{ asset('images/icons/add.png') }}" alt="NovoPost">
                </button>

                <button id="notification-btn" class="hover:opacity-60 transition-all cursor-pointer">
                    <img class="w-7 h-7" src="{{ asset('images/icons/bell.png') }}" alt="Notificacoes">
                </button>

                <x-header.menu />
            </div>
        </div>
    </div>
</header>