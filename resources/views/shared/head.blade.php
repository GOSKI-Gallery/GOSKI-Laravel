<head>
    <title>GOSKI</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
    @if (app()->environment('local'))
        <script type="module" src="http://127.0.0.1:5173/@vite/client"></script>
        <link rel="stylesheet" href="http://127.0.0.1:5173/resources/css/app.css" />
        <script type="module" src="http://127.0.0.1:5173/resources/js/app.js"></script>
    @else
        @vite('resources/css/app.css')
    @endif
</head>
