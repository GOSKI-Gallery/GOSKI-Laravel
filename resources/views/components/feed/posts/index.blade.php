@foreach ($posts as $post)
    <div class="mb-8 pb-4 border-b">
        <div class='flex flex-row sm:flex-row justify-between items-center'>
            <div class='flex justify-start items-center mb-2 sm:mb-0'>
                <img src='{{ $post['users']['profile_photo_url'] ?? asset('images/icons/icon.png') }}' alt='Profile Picture'
                    class='rounded-full w-10 h-10 object-cover'>
                <h1 class='text-shadow-2xs p-4 text-xl sm:text-2xl text-center'>{{ $post['users']['username'] }}</h1>
            </div>

            <form action="{{-- route('user.follow', $post['users']['id']) --}}" method="POST">
                @csrf
                <button
                    class="inline-flex inset-ring-1 inset-ring-white/5 justify-center bg-black hover:bg-black/30 shadow-xl px-3 py-2 rounded-md font-semibold text-white text-sm cursor-pointer">
                    Seguir
                </button>
            </form>
        </div>

        <div class='flex justify-center py-4'>
            <div class='flex flex-col justify-center items-center gap-4 w-full'>
                <div class="flex justify-center items-center w-full">
                    <img src="{{ $post['image_url'] }}" alt="Post Image" class='shadow-md rounded-md w-full md:w-4xl h-auto md:max-h-128 object-cover'>
                </div>
            </div>
        </div>

        <div class="px-2 sm:px-0">
            <p><span class='font-bold'>{{ $post['users']['username'] }}</span> {{ $post['description'] }}</p>
        </div>

        <div class='flex flex-row justify-between items-center px-2 sm:px-0 py-4'>
            <form action="{{-- route('post.like', $post['id']) --}}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center focus:bg-gray-100 focus:outline-hidden text-gray-700 focus:text-gray-900 text-sm text-left cursor-pointer">
                    <img class='w-5 h-5' src="{{ asset('images/icons/like.png') }}">
                    <h1 class='ml-2 text-black'>Curtir</h1>
                </button>
            </form>
            <span class="font-semibold text-sm">{{-- count($post['likes']) - --}} curtidas</span>
        </div>
    </div>
@endforeach