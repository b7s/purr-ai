<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Native\Desktop\Facades\System;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureTimezone();
    }

    private function configureTimezone(): void
    {
        try {
            $timezone = \App\Models\Setting::get('timezone');

            if (empty($timezone)) {
                $timezone = System::timezone();
            }

            // Validate timezone
            if (! in_array($timezone, timezone_identifiers_list(), true)) {
                $timezone = config('app.timezone');
            }

            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        } catch (\Exception) {
        }
    }
}
