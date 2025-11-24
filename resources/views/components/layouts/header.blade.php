<header class="chat-header">
    @if(!is_native())
        {{-- Web mode: Logo on left --}}
        <div class="flex items-center gap-3">
            <div class="purr-ai-logo">
                <img src="/storage/images/logo-PurrAI-64.webp" alt="{{ __('chat.title') }}" class="w-full h-full">
            </div>
            <span class="text-sm font-medium tracking-wide opacity-80">{{ config('app.name') }}</span>
        </div>
    @else
        {{-- Native mode: macOS = controls left, Windows/Linux = logo left --}}
        <div class="flex items-center gap-3">
            @if(is_mac())
                <x-ui.window-controls />
            @endif
            <div class="purr-ai-logo {{ is_mac() ? 'ml-2' : '' }}">
                <img src="/storage/images/logo-PurrAI-64.webp" alt="{{ __('chat.title') }}" class="w-full h-full">
            </div>
            <span class="text-sm font-medium tracking-wide opacity-80">{{ config('app.name') }}</span>
        </div>
    @endif

    <div class="flex items-center gap-1">
        {{ $slot }}

        @if(is_native() && !is_mac())
            {{-- Windows/Linux: controls on right --}}
            <x-ui.window-controls />
        @endif
    </div>
</header>