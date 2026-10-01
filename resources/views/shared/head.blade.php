<head>
    <title>GOSKI</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://a.basemaps.cartocdn.com" crossorigin>
    <link rel="preconnect" href="https://b.basemaps.cartocdn.com" crossorigin>
    <link rel="preconnect" href="https://c.basemaps.cartocdn.com" crossorigin>
    <link rel="preconnect" href="https://d.basemaps.cartocdn.com" crossorigin>
    <link rel="preconnect" href="https://nominatim.openstreetmap.org" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <script>
        (function() {
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
