<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
<el-dropdown class="inline-block">
    <button class="flex items-center gap-2 hover:bg-gray-50 px-2 py-1 rounded-full transition cursor-pointer">
        <img class="w-8 h-8 rounded-full border border-gray-200 object-cover"
            src="{{ Auth::user()->profile_picture ?? asset('images/icons/icon.png') }}" alt="ProfilePicture">
        <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-gray-400">
            <path
                d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" />
        </svg>
    </button>

    <el-menu anchor="bottom end" popover class="!bg-white/5 shadow-lg rounded-md border border-white/20 w-56 ...">
        <div class='py-1'>
            <a href="#"
                class="flex items-center focus:bg-gray-100 px-4 py-2 focus:outline-hidden text-gray-700 focus:text-gray-900 text-sm">
                <img class='w-5 h-5' src="{{ asset('images/icons/icon.png') }}">
                <h1 class='ml-2 text-black'>Meu perfil</h1>
            </a>

            <form action="/logout" method="POST">
                <button type="submit"
                    class="flex items-center focus:bg-gray-100 px-4 py-2 focus:outline-hidden w-full text-gray-700 focus:text-gray-900 text-sm text-left cursor-pointer">
                    <img class='w-5 h-5' src="{{ asset('images/icons/exit.png') }}">
                    <h1 class='ml-2 text-black'>Sair</h1>
                </button>
            </form>
        </div>
    </el-menu>
</el-dropdown>
