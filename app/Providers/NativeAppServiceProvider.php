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
        Dock::icon(storage_path('/app/images/logo-PurrAI-256.webp'));

        Window::open()
            ->width(800)
            ->height(600)
            ->minWidth(480)
            ->minHeight(550)
            ->title(config('app.name', 'PurrAI'))
            ->titleBarHidden()
            ->resizable()
            ->alwaysOnTop(false)
            ->transparent()
            ->vibrancy('dark');
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
