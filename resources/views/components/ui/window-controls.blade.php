@if(is_mac())
    {{-- macOS style window controls (left side) --}}
    <div class="window-controls window-controls-mac">
        <button onclick="if(window.Native?.window) window.Native.window.close(); else window.close();"
            class="window-btn-close-mac" title="Close">
            <span class="window-btn-dot"></span>
        </button>
        <button onclick="if(window.Native?.window) window.Native.window.minimize();" class="window-btn-minimize-mac"
            title="Minimize">
            <span class="window-btn-dot"></span>
        </button>
        <button onclick="if(window.Native?.window) window.Native.window.maximize();" class="window-btn-maximize-mac"
            title="Maximize">
            <span class="window-btn-dot"></span>
        </button>
    </div>
@else
    {{-- Windows/Linux style window controls (right side) --}}
    <div class="window-controls window-controls-default">
        <button onclick="if(window.Native?.window) window.Native.window.minimize();" class="window-btn-minimize"
            title="Minimize">
            <i class="iconoir-minus"></i>
        </button>
        <button onclick="if(window.Native?.window) window.Native.window.maximize();" class="window-btn-maximize"
            title="Maximize">
            <i class="iconoir-square"></i>
        </button>
        <button onclick="if(window.Native?.window) window.Native.window.close(); else window.close();"
            class="window-btn-close" title="Close">
            <i class="iconoir-xmark"></i>
        </button>
    </div>
@endif