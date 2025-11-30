<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? config('app.name')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body id="app" class="flex items-center justify-center <?php echo e(!is_native() ? 'web-mode' : ''); ?>">
    <script>
        function checkMaximized() {
            const tolerance = 5;
            return Math.abs(window.innerWidth - screen.availWidth) < tolerance &&
                Math.abs(window.innerHeight - screen.availHeight) < tolerance;
        }

        // Initialize window opacity, blur and transparency settings IMMEDIATELY (before any rendering)
        (function () {
            let opacity = <?php echo e(\App\Models\Setting::get('window_opacity', config('purrai.window.opacity'))); ?>;
            let blur = <?php echo e(\App\Models\Setting::get('window_blur', config('purrai.window.blur', 48))); ?>;
            let disableTransparency = <?php echo e(\App\Models\Setting::get('disable_transparency_maximized', true) ? 'true' : 'false'); ?>;
            let isMaximized = checkMaximized();

            localStorage.setItem('window_opacity', opacity);
            localStorage.setItem('window_blur', blur);
            localStorage.setItem('disable_transparency_maximized', disableTransparency);

            if (!isMaximized) {
                isMaximized = 'false';
                localStorage.setItem('is_window_maximized', isMaximized);
            }

            // Apply correct opacity based on maximized state
            if (isMaximized === 'true' && disableTransparency === 'true') {
                document.documentElement.style.setProperty('--window-opacity', 1);
                document.documentElement.style.setProperty('--window-blur', '0px');
            } else {
                document.documentElement.style.setProperty('--window-opacity', opacity / 100);
                // Only apply blur if opacity is less than 100%
                if (opacity < 100) {
                    document.documentElement.style.setProperty('--window-blur', blur + 'px');
                } else {
                    document.documentElement.style.setProperty('--window-blur', '0px');
                }
            }
        })();

        // Initialize theme mode - MUST run before any rendering
        (function () {
            const themeMode = '<?php echo e(\App\Models\Setting::get("theme_mode", "automatic")); ?>';
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
        <?php if (isset($component)) { $__componentOriginal5d959a11ff826b3fd89e60f60adf4cb2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5d959a11ff826b3fd89e60f60adf4cb2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-header','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <?php echo e($headerActions ?? ''); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5d959a11ff826b3fd89e60f60adf4cb2)): ?>
<?php $attributes = $__attributesOriginal5d959a11ff826b3fd89e60f60adf4cb2; ?>
<?php unset($__attributesOriginal5d959a11ff826b3fd89e60f60adf4cb2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5d959a11ff826b3fd89e60f60adf4cb2)): ?>
<?php $component = $__componentOriginal5d959a11ff826b3fd89e60f60adf4cb2; ?>
<?php unset($__componentOriginal5d959a11ff826b3fd89e60f60adf4cb2); ?>
<?php endif; ?>

        <div class="page-content">
            <?php echo e($slot); ?>

        </div>
    </main>

    <?php if (isset($component)) { $__componentOriginal339c7fedf680433726dbafc2f156956f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal339c7fedf680433726dbafc2f156956f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.toast','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal339c7fedf680433726dbafc2f156956f)): ?>
<?php $attributes = $__attributesOriginal339c7fedf680433726dbafc2f156956f; ?>
<?php unset($__attributesOriginal339c7fedf680433726dbafc2f156956f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal339c7fedf680433726dbafc2f156956f)): ?>
<?php $component = $__componentOriginal339c7fedf680433726dbafc2f156956f; ?>
<?php unset($__componentOriginal339c7fedf680433726dbafc2f156956f); ?>
<?php endif; ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


    <script>
        // Track if window is maximized (load from localStorage)
        var isWindowMaximized = localStorage.getItem('is_window_maximized') === 'true';

        // Check and apply transparency settings on page load (make it global)
        window.checkTransparencySettings = function () {
            const disableTransparency = localStorage.getItem('disable_transparency_maximized') === 'true';
            const userOpacity = parseInt(localStorage.getItem('window_opacity')) || 90;
            const userBlur = parseInt(localStorage.getItem('window_blur')) || 48;

            // If window is maximized and transparency should be disabled
            if (isWindowMaximized && disableTransparency) {
                document.documentElement.style.setProperty('--window-opacity', 1);
                document.documentElement.style.setProperty('--window-blur', '0px');
            } else if (!isWindowMaximized) {
                document.documentElement.style.setProperty('--window-opacity', userOpacity / 100);
                // Only apply blur if opacity is less than 100%
                if (userOpacity < 100) {
                    document.documentElement.style.setProperty('--window-blur', userBlur + 'px');
                } else {
                    document.documentElement.style.setProperty('--window-blur', '0px');
                }
            }
        }

        // Listen for opacity and theme changes from Settings page
        document.addEventListener('livewire:init', () => {
            // Check settings on initial load
            window.checkTransparencySettings();
            Livewire.on('opacity-changed', (event) => {
                const opacity = event.opacity;
                const blur = parseInt(localStorage.getItem('window_blur')) || 48;
                localStorage.setItem('window_opacity', opacity);

                // Only apply if not maximized or transparency is not disabled
                if (!isWindowMaximized) {
                    document.documentElement.style.setProperty('--window-opacity', opacity / 100);
                    // Only apply blur if opacity is less than 100%
                    if (opacity < 100) {
                        document.documentElement.style.setProperty('--window-blur', blur + 'px');
                    } else {
                        document.documentElement.style.setProperty('--window-blur', '0px');
                    }
                }
            });

            Livewire.on('blur-changed', (event) => {
                const blur = event.blur;
                const opacity = parseInt(localStorage.getItem('window_opacity')) || 90;
                localStorage.setItem('window_blur', blur);

                // Only apply blur if opacity is less than 100% and not maximized
                if (!isWindowMaximized && opacity < 100) {
                    document.documentElement.style.setProperty('--window-blur', blur + 'px');
                } else {
                    document.documentElement.style.setProperty('--window-blur', '0px');
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

                checkTransparencySettings();
            });

            // Restore user's opacity when unmaximized
            Livewire.on('restore-opacity', (event) => {
                isWindowMaximized = false;
                localStorage.setItem('is_window_maximized', 'false');
                localStorage.setItem('window_opacity', event.opacity);

                checkTransparencySettings();
            });

            // Handle transparency setting changes
            Livewire.on('transparency-setting-changed', (event) => {
                const enabled = event.enabled;
                const userOpacity = parseInt(localStorage.getItem('window_opacity')) || 90;
                const userBlur = parseInt(localStorage.getItem('window_blur')) || 48;
                localStorage.setItem('disable_transparency_maximized', enabled ? 'true' : 'false');

                if (isWindowMaximized) {
                    if (enabled) {
                        // Enable: set to 100% opacity and no blur
                        document.documentElement.style.setProperty('--window-opacity', 1);
                        document.documentElement.style.setProperty('--window-blur', '0px');
                    } else {
                        // Disable: restore user's setting
                        document.documentElement.style.setProperty('--window-opacity', userOpacity / 100);
                        // Only apply blur if opacity is less than 100%
                        if (userOpacity < 100) {
                            document.documentElement.style.setProperty('--window-blur', userBlur + 'px');
                        } else {
                            document.documentElement.style.setProperty('--window-blur', '0px');
                        }
                    }
                }
            });

            // Reset window state when chat component loads (first page)
            Livewire.on('reset-window-state', () => {
                // Reset maximized state to false
                isWindowMaximized = false;
                localStorage.setItem('is_window_maximized', 'false');

                // Apply user's opacity and blur settings
                const userOpacity = parseInt(localStorage.getItem('window_opacity')) || 90;
                const userBlur = parseInt(localStorage.getItem('window_blur')) || 48;
                document.documentElement.style.setProperty('--window-opacity', userOpacity / 100);
                // Only apply blur if opacity is less than 100%
                if (userOpacity < 100) {
                    document.documentElement.style.setProperty('--window-blur', userBlur + 'px');
                } else {
                    document.documentElement.style.setProperty('--window-blur', '0px');
                }
            });

            // Re-check settings on Livewire navigation
            Livewire.hook('navigated', () => {
                window.checkTransparencySettings();
            });
        });
        checkTransparencySettings();
    </script>
</body>

</html><?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/components/layouts/app.blade.php ENDPATH**/ ?>