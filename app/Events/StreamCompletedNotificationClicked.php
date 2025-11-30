<?php

declare(strict_types=1);

namespace App\Events;

use Native\Desktop\Events\Notifications\NotificationClicked;
use Native\Desktop\Facades\Window;

class StreamCompletedNotificationClicked extends NotificationClicked
{
    public function handle(): void
    {
        try {
            Window::show(config('purrai.window.main_id'));
            Window::open(config('purrai.window.main_id'));
        } catch (\Exception $e) {
        }
    }
}
