<header class="chat-header">
    @if(!is_native())
        <div class="flex items-center gap-3">
            <a href="{{ route('chat') }}" class="purr-ai-logo hover:opacity-80 transition-opacity duration-200">
                <img src="{{ asset('images/logo-PurrAI-64.webp') }}" alt="{{ __('chat.title') }}" class="w-full h-full">
            </a>
            <span class="text-sm font-medium tracking-wide opacity-80">{{ config('app.name') }}</span>
        </div>
    @else
        {{-- Native mode: macOS = controls left, Windows/Linux = logo left --}}
        <div class="flex items-center gap-3">
            @if(is_mac())
                <x-ui.window-controls />
            @endif
            <a href="{{ route('chat') }}"
                class="purr-ai-logo {{ is_mac() ? 'ml-2' : '' }} hover:opacity-80 transition-opacity duration-200">
                <img src="{{ asset('images/logo-PurrAI-64.webp') }}" alt="{{ __('chat.title') }}" class="w-full h-full">
            </a>
            <span class="text-sm font-medium tracking-wide opacity-80">{{ config('app.name') }}</span>
        </div>
    @endif

    <div class="flex items-center gap-1">
        {{ $slot }}
        <a href="{{ route('settings') }}" wire:navigate>
            <x-ui.icon-button icon="settings" :title="__('ui.tooltips.settings')" />
        </a>

        @if(is_native() && !is_mac())
            <x-ui.window-controls />
        @endif
    </div>
</header>