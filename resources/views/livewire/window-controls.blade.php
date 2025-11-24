@if(is_mac())
    {{-- macOS style window controls (left side) --}}
    <div class="window-controls window-controls-mac">
        <button onclick="handleWindowClose()" class="window-btn-close-mac" title="{{ __('ui.window.close') }}">
            <span class="window-btn-dot"></span>
        </button>
        <button onclick="handleWindowMinimize()" class="window-btn-minimize-mac" title="{{ __('ui.window.minimize') }}">
            <span class="window-btn-dot"></span>
        </button>
        <button onclick="handleWindowMaximize()" class="window-btn-maximize-mac" title="{{ __('ui.window.maximize') }}">
            <span class="window-btn-dot"></span>
        </button>
    </div>
@else
    {{-- Windows/Linux style window controls (right side) --}}
    <div class="window-controls window-controls-default">
        <button onclick="handleWindowMinimize()" class="window-btn" title="{{ __('ui.window.minimize') }}">
            <i class="iconoir-minus text-xl"></i>
        </button>
        <button onclick="handleWindowMaximize()" class="window-btn" title="{{ __('ui.window.maximize') }}">
            <i class="iconoir-square text-xl"></i>
        </button>
        <button onclick="handleWindowClose()" class="window-btn close" title="{{ __('ui.window.close') }}">
            <i class="iconoir-xmark text-xl"></i>
        </button>
    </div>
@endif

@script
<script>
    window.handleWindowMinimize = function () {
        @this.call('minimize');
    };

    window.handleWindowClose = function () {
        @this.call('close');
    };

    window.handleWindowMaximize = function () {
        // Use NativePHP's Window API to get current bounds
        if (window.Native && window.Native.window) {
            window.Native.window.getBounds().then((bounds) => {
                const screenWidth = window.screen.availWidth;
                const screenHeight = window.screen.availHeight;

                @this.call('toggleMaximize',
                    screenWidth,
                    screenHeight,
                    bounds.width,
                    bounds.height,
                    bounds.x,
                    bounds.y
                );
            }).catch((error) => {
                console.error('Failed to get window bounds:', error);
                // Fallback to default values
                @this.call('toggleMaximize',
                    window.screen.availWidth,
                    window.screen.availHeight,
                    800,
                    600,
                    100,
                    100
                );
            });
        } else {
            // Fallback for non-native environment
            @this.call('toggleMaximize',
                window.screen.availWidth,
                window.screen.availHeight,
                800,
                600,
                100,
                100
            );
        }
    };
</script>
@endscript