<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\StreamCompletedNotificationClicked;
use App\Livewire\Settings;
use App\Services\ChatService;
use App\Services\UpdateService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Events\Windows\WindowClosed;
use Native\Desktop\Facades\Dock;
use Native\Desktop\Facades\Menu;
use Native\Desktop\Facades\MenuBar;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    private const int MENUBAR_WIDTH = 480;

    private const int MENUBAR_HEIGHT = 550;

    private const int MAIN_WINDOW_WIDTH = 900;

    private const int MAIN_WINDOW_HEIGHT = 650;

    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $this->setGlobalEventListener();
        $this->registerUpdateEvents();
        $this->checkForUpdatesIfNeeded();

        Dock::icon(public_path('icon.png'));

        // @todo How do I make this work?
        // $this->createMenuBar();
        // $this->registerMenuEvents();
        $this->openMainWindow();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }

    /**
     * Create the menu bar with icon, context menu, and window.
     */
    private function createMenuBar(): void
    {
        MenuBar::create()
            ->icon(public_path('IconTemplate.png'))
            ->tooltip(config('app.name', 'PurrAI'))
            ->transparent()
            ->width(config('purrai.window.min_width', self::MENUBAR_WIDTH))
            ->height(config('purrai.window.min_height', self::MENUBAR_HEIGHT))
            ->url($this->initialRoute('menuBar'))
            ->trayCenter()
            ->resizable(true)
            ->alwaysOnTop(true)
            ->showDockIcon(false)
            ->withContextMenu($this->buildContextMenu());
    }

    /**
     * Build the context menu for right-click on menu bar icon.
     */
    private function buildContextMenu(): \Native\Desktop\Menu\Menu
    {
        $menu = Menu::make(
            Menu::label(config('app.name', 'PurrAI')),
            Menu::separator(),
            Menu::label(__('ui.menu.open_chat'))->event('App\\Events\\MenuOpenChat'),
            Menu::label(__('ui.menu.settings'))->event('App\\Events\\MenuOpenSettings'),
            Menu::separator(),
            Menu::quit(__('ui.menu.quit'))
        );

        return $menu;
    }

    /**
     * Register event listeners for menu actions.
     */
    private function registerMenuEvents(): void
    {
        // Listen for right-click event
        Event::listen('Native\\Desktop\\Events\\MenuBar\\MenuBarRightClicked', function ($event): void {
            Log::info('MenuBar right-clicked', [
                'event' => get_class($event),
                'payload' => method_exists($event, 'toArray') ? $event->toArray() : 'no payload',
            ]);
        });

        Event::listen('App\\Events\\MenuOpenChat', function (): void {
            Log::info('MenuOpenChat event triggered');
            $this->openMainWindow(route('chat'));
        });

        Event::listen('App\\Events\\MenuOpenSettings', function (): void {
            Log::info('MenuOpenSettings event triggered');
            $this->openMainWindow(route('settings'));
        });
    }

    private function initialRoute(string $type = 'chat'): string
    {
        $ChatService = app(ChatService::class);

        return $ChatService->getInitialRoute($type);
    }

    /**
     * Register update-related event listeners.
     */
    private function registerUpdateEvents(): void
    {
        Event::listen('Native\\Desktop\\Events\\Updater\\UpdateAvailable', function ($event): void {
            $updateService = app(UpdateService::class);
            $updateService->markUpdateAvailable($event->version ?? 'unknown');

            Log::info('Update available', ['version' => $event->version ?? 'unknown']);
        });

        Event::listen('Native\\Desktop\\Events\\Updater\\UpdateDownloaded', function ($event): void {
            Log::info('Update downloaded', ['version' => $event->version ?? 'unknown']);
        });

        Event::listen('Native\\Desktop\\Events\\Updater\\UpdateNotAvailable', function (): void {
            $updateService = app(UpdateService::class);
            $updateService->clearUpdateAvailable();

            Log::info('No update available');
        });
    }

    /**
     * Check for updates if the interval has passed.
     */
    private function checkForUpdatesIfNeeded(): void
    {
        $updateService = app(UpdateService::class);

        if ($updateService->shouldCheckForUpdates()) {
            $updateService->checkForUpdates();
        }
    }

    private function setGlobalEventListener(): void
    {
        Event::listen(StreamCompletedNotificationClicked::class, function (StreamCompletedNotificationClicked $event) {
            $event->handle();
        });

        Event::listen(WindowClosed::class, function () {
            Settings::deleteCaches();
        });
    }

    /**
     * Open the main application window.
     */
    private function openMainWindow(?string $url = null): void
    {
        $url ??= $this->initialRoute();

        Window::open(config('purrai.window.main_id', 'main'))
            ->url($url)
            ->width(config('purrai.window.default_width', self::MAIN_WINDOW_WIDTH))
            ->height(config('purrai.window.default_height', self::MAIN_WINDOW_HEIGHT))
            ->minWidth(config('purrai.window.min_width', self::MENUBAR_WIDTH))
            ->minHeight(config('purrai.window.min_height', self::MENUBAR_HEIGHT))
            ->title(config('app.name', 'PurrAI'))
            ->titleBarHidden()
            ->transparent()
            ->resizable();
    }
}
