<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Session;
use Livewire\Component;
use Native\Desktop\Facades\Window;

class WindowControls extends Component
{
    #[Session]
    public bool $isMaximized = false;

    #[Session]
    public ?int $previousWidth = null;

    #[Session]
    public ?int $previousHeight = null;

    #[Session]
    public ?int $previousX = null;

    #[Session]
    public ?int $previousY = null;

    public function minimize(): void
    {
        Window::minimize('main');
    }

    public function toggleMaximize(
        ?int $screenWidth = null,
        ?int $screenHeight = null,
        ?int $currentWidth = null,
        ?int $currentHeight = null,
        ?int $currentX = null,
        ?int $currentY = null
    ): void {
        $disableTransparency = (bool) \App\Models\Setting::get('disable_transparency_maximized', true);

        if ($this->isMaximized) {
            // Restore to previous size
            $width = $this->previousWidth ?? config('purrai.window.default_width', 800);
            $height = $this->previousHeight ?? config('purrai.window.default_height', 600);
            $x = $this->previousX ?? 100;
            $y = $this->previousY ?? 100;

            Window::resize($width, $height, 'main');
            Window::position($x, $y, false, 'main');

            $this->isMaximized = false;

            // Restore user's opacity setting
            if ($disableTransparency) {
                $userOpacity = (int) \App\Models\Setting::get('window_opacity', config('purrai.window.opacity'));
                $this->dispatch('restore-opacity', opacity: $userOpacity);
            }
        } else {
            // Save current size and position
            $this->previousWidth = $currentWidth ?? config('purrai.window.default_width', 800);
            $this->previousHeight = $currentHeight ?? config('purrai.window.default_height', 600);
            $this->previousX = $currentX ?? 100;
            $this->previousY = $currentY ?? 100;

            // Maximize to screen size
            $maxWidth = $screenWidth ?? 1280;
            $maxHeight = $screenHeight ?? 720;

            Window::position(0, 0, false, 'main');
            Window::resize($maxWidth, $maxHeight, 'main');

            $this->isMaximized = true;

            // Set opacity to 100% if option is enabled
            if ($disableTransparency) {
                $this->dispatch('set-opacity-maximized');
            }
        }
    }

    public function isMaximized(): bool
    {
        return $this->isMaximized;
    }

    public function close(): void
    {
        Window::close('main');
    }

    public function render()
    {
        return view('livewire.window-controls');
    }
}
