<head>
    <title>GOSKI</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ECECEC">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
    <script>
        (function(){var k='goski-theme',d='dark';function g(){try{return localStorage.getItem(k)}catch{return null}}function a(t){var i=t===d||t!=='light'&&window.matchMedia('(prefers-color-scheme:dark)').matches===!0;document.documentElement.classList.toggle(d,i);try{localStorage.setItem(k,i?d:'light')}catch{}var m=document.querySelector('meta[name="theme-color"]');if(m)m.content=i?'#09090b':'#ECECEC'}var s=g();a(s||'system')})();
    </script>
    @vite('resources/css/app.css')
</head>