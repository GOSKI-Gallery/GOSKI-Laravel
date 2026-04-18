@props(['notifications'])

<div id="notification-modal" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden">
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-8 rounded-lg">
        <h2 class="text-xl font-bold mb-4">Notifications</h2>
        <div id="notification-content">
            @foreach ($notifications as $notification)
                <p>{{ $notification }}</p>
            @endforeach
        </div>
        <button id="close-modal" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Close</button>
    </div>
</div>