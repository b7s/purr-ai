<header class="top-header">
    <div class="flex items-center gap-3">
        @if(is_native() && is_mac())
            <livewire:window-controls :key="'window-controls-mac'" />
        @endif

        <a href="{{ route('chat') }}"
            class="purr-ai-logo {{ is_native() && is_mac() ? 'ml-2' : '' }} hover:opacity-80 transition-opacity duration-200">
            <img src="{{ asset('images/logo-PurrAI-64.webp') }}" alt="{{ __('chat.title') }}" class="w-full h-full">
        </a>

        <span class="window-title">{{ config('app.name') }}</span>
    </div>

    <div class="flex items-center gap-1">
        {{ $slot }}

        <a href="{{ route('settings') }}" wire:navigate>
            <x-ui.icon-button icon="settings" :title="__('ui.tooltips.settings')" />
        </a>

        @if(is_native() && !is_mac())
            <livewire:window-controls :key="'window-controls'" />
        @endif
    </div>
</header>