<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;
use Native\Desktop\Facades\AutoUpdater;

class UpdateService
{
    private const int CHECK_INTERVAL_HOURS = 6;

    public function shouldCheckForUpdates(): bool
    {
        $lastCheck = Setting::get('last_update_check');

        if (! $lastCheck) {
            return true;
        }

        try {
            $lastCheckTime = Carbon::parse($lastCheck);
            $hoursSinceLastCheck = abs(now()->diffInHours($lastCheckTime));

            return $hoursSinceLastCheck >= self::CHECK_INTERVAL_HOURS;
        } catch (\Exception) {
            return true;
        }
    }

    public function checkForUpdates(): void
    {
        try {
            AutoUpdater::checkForUpdates();
            Setting::set('last_update_check', now()->toDateTimeString());
        } catch (\Exception) {
        }
    }

    public function markUpdateAvailable(string $version): void
    {
        Setting::set('update_available', true);
        Setting::set('update_version', $version);
    }

    public function clearUpdateAvailable(): void
    {
        Setting::set('update_available', false);
        Setting::set('update_version', null);
    }

    public function getLastCheckTime(): ?Carbon
    {
        $lastCheck = Setting::get('last_update_check');

        if (! $lastCheck) {
            return null;
        }

        try {
            return Carbon::parse($lastCheck);
        } catch (\Exception) {
            return null;
        }
    }

    public function getCurrentVersion(): string
    {
        return config('nativephp.version', '1.0.0');
    }

    public function getUpdateVersion(): ?string
    {
        return Setting::get('update_version');
    }

    public function isUpdateAvailable(): bool
    {
        return (bool) Setting::get('update_available', false);
    }
}
