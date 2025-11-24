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
    <script>
        function checkMaximized() {
            const tolerance = 4;
            return Math.abs(window.innerWidth - screen.availWidth) < tolerance &&
                Math.abs(window.innerHeight - screen.availHeight) < tolerance;
        }

        // Initialize window opacity and transparency settings IMMEDIATELY (before any rendering)
        (function () {
            let opacity = {{ \App\Models\Setting::get('window_opacity', config('purrai.window.opacity')) }};
            let disableTransparency = {{ \App\Models\Setting::get('disable_transparency_maximized', true) ? 'true' : 'false' }};
            let isMaximized = checkMaximized();

            localStorage.setItem('window_opacity', opacity);
            localStorage.setItem('disable_transparency_maximized', disableTransparency);

            if (!isMaximized) {
                isMaximized = 'false';
                localStorage.setItem('is_window_maximized', isMaximized);
            }

            // Apply correct opacity based on maximized state
            if (isMaximized === 'true' && disableTransparency === 'true') {
                document.documentElement.style.setProperty('--window-opacity', 1);
            } else {
                document.documentElement.style.setProperty('--window-opacity', opacity / 100);
            }
        })();

        // Initialize theme mode - MUST run before any rendering
        (function () {
            const themeMode = '{{ \App\Models\Setting::get("theme_mode", "automatic") }}';
            const html = document.documentElement;

            // Force remove dark class first
            html.classList.remove('dark');

            if (themeMode === 'dark') {
                html.classList.add('dark');
            } else if (themeMode === 'automatic') {
                // Automatic - follow system preference
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    html.classList.add('dark');
                }
            }
            // If light mode, dark class is already removed above
        })();
    </script>

    <main class="glass-panel">
        <x-app-header>
            {{ $headerActions ?? '' }}
        </x-app-header>

        {{ $slot }}
    </main>

    @livewireScripts

    <script>
        // Track if window is maximized (load from localStorage)
        var isWindowMaximized = localStorage.getItem('is_window_maximized') === 'true';

        // Check and apply transparency settings on page load (make it global)
        window.checkTransparencySettings = function () {
            const disableTransparency = localStorage.getItem('disable_transparency_maximized') === 'true';
            const userOpacity = parseInt(localStorage.getItem('window_opacity')) || 90;

            // If window is maximized and transparency should be disabled
            if (isWindowMaximized && disableTransparency) {
                document.documentElement.style.setProperty('--window-opacity', 1);
            } else if (!isWindowMaximized) {
                document.documentElement.style.setProperty('--window-opacity', userOpacity / 100);
            }
        }

        // Listen for opacity and theme changes from Settings page
        document.addEventListener('livewire:init', () => {
            // Check settings on initial load
            window.checkTransparencySettings();
            Livewire.on('opacity-changed', (event) => {
                const opacity = event.opacity;
                localStorage.setItem('window_opacity', opacity);

                // Only apply if not maximized or transparency is not disabled
                if (!isWindowMaximized) {
                    document.documentElement.style.setProperty('--window-opacity', opacity / 100);
                }
            });

            Livewire.on('theme-changed', (event) => {
                const theme = event.theme;
                const html = document.documentElement;

                // Force remove dark class first
                html.classList.remove('dark');

                if (theme === 'dark') {
                    html.classList.add('dark');
                } else if (theme === 'automatic') {
                    // Automatic - follow system preference
                    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        html.classList.add('dark');
                    }
                }
                // If light mode, dark class is already removed above
            });

            // Set opacity to 100% when maximized
            Livewire.on('set-opacity-maximized', () => {
                isWindowMaximized = true;
                localStorage.setItem('is_window_maximized', 'true');
                localStorage.setItem('window_opacity', 1);

                checkTransparencySettings()
            });

            // Restore user's opacity when unmaximized
            Livewire.on('restore-opacity', (event) => {
                isWindowMaximized = false;
                localStorage.setItem('is_window_maximized', 'false');
                localStorage.setItem('window_opacity', event.opacity);

                checkTransparencySettings()
            });

            // Handle transparency setting changes
            Livewire.on('transparency-setting-changed', (event) => {
                const enabled = event.enabled;
                localStorage.setItem('disable_transparency_maximized', enabled ? 'true' : 'false');

                if (isWindowMaximized) {
                    if (enabled) {
                        // Enable: set to 100%
                        document.documentElement.style.setProperty('--window-opacity', 1);
                    } else {
                        // Disable: restore user's setting
                        const userOpacity = localStorage.getItem('window_opacity') || 90;
                        document.documentElement.style.setProperty('--window-opacity', userOpacity / 100);
                    }
                }
            });

            // Reset window state when chat component loads (first page)
            Livewire.on('reset-window-state', () => {
                // Reset maximized state to false
                isWindowMaximized = false;
                localStorage.setItem('is_window_maximized', 'false');

                // Apply user's opacity setting
                const userOpacity = localStorage.getItem('window_opacity') || 90;
                document.documentElement.style.setProperty('--window-opacity', userOpacity / 100);
            });

            // Re-check settings on Livewire navigation
            Livewire.hook('navigated', () => {
                window.checkTransparencySettings();
            });
        });
        checkTransparencySettings();
    </script>
</body>

</html>