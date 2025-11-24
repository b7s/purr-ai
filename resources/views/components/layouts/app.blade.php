<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body id="app" class="flex items-center justify-center {{ !is_native() ? 'web-mode' : '' }}">
    <main class="glass-panel">
        {{ $slot }}
    </main>

    @livewireScripts

    <script>
        // Theme toggle support
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // Initialize window opacity from settings
        document.addEventListener('DOMContentLoaded', () => {
            fetch('/api/settings/window-opacity')
                .then(response => response.json())
                .then(data => {
                    if (data.opacity) {
                        document.documentElement.style.setProperty('--window-opacity', data.opacity / 100);
                    }
                })
                .catch(() => {
                    // Use default opacity if fetch fails
                });
        });

        // Listen for opacity changes
        document.addEventListener('livewire:init', () => {
            Livewire.on('opacity-changed', (event) => {
                const opacity = event.opacity / 100;
                document.documentElement.style.setProperty('--window-opacity', opacity);
            });
        });

    </script>
</body>

</html>