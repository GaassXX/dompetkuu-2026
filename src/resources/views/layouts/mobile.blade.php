<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dompetkuu' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <div class="max-w-md mx-auto min-h-screen relative bg-gray-50">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
