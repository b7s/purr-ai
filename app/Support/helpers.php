<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Services\ChatService;
use App\Services\WhisperService;
use Native\Desktop\Facades\Window;

if (! function_exists('is_native')) {
    /**
     * Check if the application is running in native mode (NativePHP/Electron).
     */
    function is_native(): bool
    {
        if (! class_exists(\Native\Desktop\Facades\Window::class)) {
            return false;
        }

        try {
            return app()->bound('native') ||
                   defined('NATIVE_PHP_RUNNING') && NATIVE_PHP_RUNNING === true ||
                   isset($_SERVER['NATIVE_PHP']) && $_SERVER['NATIVE_PHP'] === '1' ||
                   isset($_ENV['NATIVE_PHP']) && $_ENV['NATIVE_PHP'] === '1' ||
                   str_contains(request()->userAgent() ?? '', 'Electron');
        } catch (\Throwable) {
            return false;
        }
    }
}

if (! function_exists('is_mac')) {
    /**
     * Check if the application is running on macOS.
     */
    function is_mac(): bool
    {
        return PHP_OS_FAMILY === 'Darwin';
    }
}

if (! function_exists('is_windows')) {
    /**
     * Check if the application is running on Windows.
     */
    function is_windows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }
}

if (! function_exists('is_linux')) {
    /**
     * Check if the application is running on Linux.
     */
    function is_linux(): bool
    {
        return PHP_OS_FAMILY === 'Linux';
    }
}

if (! function_exists('is_menubar')) {
    /**
     * Check if the current request is from the menubar window.
     */
    function is_menubar(): bool
    {
        try {
            // @todo Is there any way to identify if you are in a menubar window?
            Window::current();

            return false;
        } catch (\Exception $e) {
            if (request()->input('_windowId') === config('purrai.window.main_id', 'main')) {
                return false;
            }

            return true;
        }
    }
}

if (! function_exists('getPreviusChatUrl')) {
    function getPreviousChatUrl(): string
    {
        $previousUrl = url()->previous();

        try {
            $request = request()->create($previousUrl);
            $route = app('router')->getRoutes()->match($request);

            if ($route->getName() === 'chat' || $route->getName() === 'menubar.chat') {
                return $previousUrl;
            }
        } catch (\Throwable) {
            try {
                $typeRoute = is_menubar() ? 'menubar' : 'chat';

                return app(ChatService::class)->getInitialRoute($typeRoute);
            } catch (\Throwable) {
            }
        }

        return route('chat');
    }
}

if (! function_exists('getUserName')) {
    function getUserName(): ?string
    {
        return Setting::get('user_name');
    }
}

if (! function_exists('hasSettingsAlert')) {
    function hasSettingsAlert(): bool
    {
        return WhisperService::hasPendingConfiguration();
    }
}
