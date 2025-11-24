<?php

declare(strict_types=1);

namespace App\Providers;

use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Dock;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // Set application icon in dock/taskbar
        Dock::icon(public_path('images/logo-PurrAI-256.webp'));

        Window::open('main')
            ->width(config('purrai.window.default_width', 800))
            ->height(config('purrai.window.default_height', 600))
            ->minWidth(config('purrai.window.min_width', 480))
            ->minHeight(config('purrai.window.min_height', 550))
            ->title(config('app.name', 'PurrAI'))
            ->titleBarHidden()
            ->resizable()
            ->alwaysOnTop(false)
            ->transparent()
            ->vibrancy('dark')
            ->rememberState();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
