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
        Window::minimize();
    }

    public function toggleMaximize(
        ?int $screenWidth = null,
        ?int $screenHeight = null,
        ?int $currentWidth = null,
        ?int $currentHeight = null,
        ?int $currentX = null,
        ?int $currentY = null
    ): void {
        if ($this->isMaximized) {
            $width = $this->previousWidth ?? config('purrai.window.default_width', 800);
            $height = $this->previousHeight ?? config('purrai.window.default_height', 600);

            Window::resize($width, $height, 'main');

            if ($this->previousX !== null && $this->previousY !== null) {
                Window::position($this->previousX, $this->previousY, false, 'main');
            }

            $this->isMaximized = false;
        } else {
            $this->previousWidth = $currentWidth ?? config('purrai.window.default_width', 800);
            $this->previousHeight = $currentHeight ?? config('purrai.window.default_height', 600);
            $this->previousX = $currentX;
            $this->previousY = $currentY;

            $maxWidth = $screenWidth ?? 1920;
            $maxHeight = $screenHeight ?? 1080;

            Window::position(0, 0, false, 'main');
            Window::resize($maxWidth, $maxHeight, 'main');

            $this->isMaximized = true;
        }
    }

    public function close(): void
    {
        Window::close();
    }

    public function render()
    {
        return view('livewire.window-controls');
    }
}
