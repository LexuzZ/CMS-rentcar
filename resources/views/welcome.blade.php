<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- WAJIB -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Muat CSS dan JS. Kalau resources/js/app.js belum ada / belum butuh, boleh hapus entry-nya --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
</head>

<body class="antialiased">

    <h1 class="font-bold text-2xl">Hello</h1>

    {{-- Simple toast container (opsional, kalau mau non-blocking notif) --}}
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"></div>

    {{-- Livewire Scripts --}}
    @livewireScripts

    <script>
        window.addEventListener('notify', function (event) {
            // Defensive: pastikan detail selalu ada, walau event dipicu tanpa payload
            const { type = 'info', message = null } = event.detail ?? {};

            if (!message) {
                console.warn('[notify] Event diterima tanpa message, diabaikan.', event);
                return;
            }

            showToast(type, message);
        });

        function showToast(type, message) {
            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                warning: 'bg-yellow-500',
                info: 'bg-blue-600',
            };

            const bg = colors[type] ?? colors.info;

            const toast = document.createElement('div');
            toast.className =
                `pointer-events-auto text-white text-sm px-4 py-2 rounded shadow-lg ${bg} transition-opacity duration-300`;
            toast.textContent = message;

            const container = document.getElementById('toast-container');
            if (!container) {
                // Fallback kalau container tidak ditemukan di DOM
                alert(message);
                return;
            }

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

</body>

</html>
