<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- WAJIB -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite('resources/css/app.css')

    {{-- Livewire Styles --}}
    @livewireStyles

    <title>Laravel</title>
</head>
<body class="antialiased">

    <h1 class="font-bold text-2xl">Hello</h1>

    {{-- Livewire Scripts --}}
    @livewireScripts

    <script>
        window.addEventListener('notify', function (event) {
            const { type, message } = event.detail;
            alert(message);
        });
    </script>

</body>
</html>
