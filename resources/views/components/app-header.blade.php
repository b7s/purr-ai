<header class="top-header">
    <div class="flex items-center gap-3">
        @if(is_native() && is_mac() && !is_menubar())
            <livewire:window-controls :key="'window-controls-mac'" />
        @endif

        <div class="purr-ai-logo {{ is_native() && is_mac() ? 'ml-2' : '' }}">
            <span class="w-full h-full"
                style="background: url({{ asset('images/mascot/logo.svg') }}) center no-repeat; background-size: contain;"></span>
        </div>

        <span class="window-title">{{ config('app.name') }}</span>
    </div>

    <div class="flex items-center gap-1">
        {{ $slot }}

        <a href="{{ route('settings') }}" wire:navigate class="relative">
            <x-ui.icon-button icon="settings" :title="__('ui.tooltips.settings')" />
            @if(hasSettingsAlert())
                <span class="settings-alert-badge"></span>
            @endif
        </a>

        @if(is_native() && !is_mac() && !is_menubar())
            <livewire:window-controls :key="'window-controls'" />
        @endif
    </div>
</header>