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

    <div class="min-h-screen bg-gray-50 flex">
        <x-mobile.sidebar />
        <main class="flex-1 md:ml-64 pb-28 md:pb-0 min-w-0">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
