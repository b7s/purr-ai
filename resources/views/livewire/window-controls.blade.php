@if(is_mac())
    {{-- macOS style window controls (left side) --}}
    <div class="window-controls window-controls-mac">
        <button wire:click="close" class="window-btn-close-mac" title="{{ __('ui.window.close') }}">
            <span class="window-btn-dot"></span>
        </button>
        <button wire:click="minimize" class="window-btn-minimize-mac" title="{{ __('ui.window.minimize') }}">
            <span class="window-btn-dot"></span>
        </button>
        <button onclick="handleMaximize()" class="window-btn-maximize-mac" title="{{ __('ui.window.maximize') }}">
            <span class="window-btn-dot"></span>
        </button>
    </div>
@else
    {{-- Windows/Linux style window controls (right side) --}}
    <div class="window-controls window-controls-default">
        <button wire:click="minimize" class="window-btn" title="{{ __('ui.window.minimize') }}">
            <i class="iconoir-minus text-xl"></i>
        </button>
        <button onclick="handleMaximize()" class="window-btn" title="{{ __('ui.window.maximize') }}">
            <i class="iconoir-square text-xl"></i>
        </button>
        <button wire:click="close" class="window-btn close" title="{{ __('ui.window.close') }}">
            <i class="iconoir-xmark text-xl"></i>
        </button>
    </div>
@endif

<script>
    function handleMaximize() {
        const screenWidth = window.screen.availWidth;
        const screenHeight = window.screen.availHeight;
        const currentWidth = document.querySelector('#app').offsetWidth;
        const currentHeight = document.querySelector('#app').offsetHeight;
        const currentX = window.screenX;
        const currentY = window.screenY;

        @this.call('toggleMaximize', screenWidth, screenHeight, currentWidth, currentHeight, currentX, currentY);
    }
</script>