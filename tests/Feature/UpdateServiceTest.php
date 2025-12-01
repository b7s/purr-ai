<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Services\UpdateService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Setting::set('update_available', false);
    Setting::set('update_version', null);
    Setting::set('last_update_check', null);
});

it('should check for updates when never checked before', function (): void {
    $service = new UpdateService;

    expect($service->shouldCheckForUpdates())->toBeTrue();
});

it('should check for updates when more than 6 hours passed', function (): void {
    $checkTime = now()->subHours(7)->toDateTimeString();
    Setting::set('last_update_check', $checkTime);
    cache()->forget('settings.last_update_check');

    $service = new UpdateService;

    expect($service->shouldCheckForUpdates())->toBeTrue();
});

it('should not check for updates when less than 6 hours passed', function (): void {
    Setting::set('last_update_check', now()->subHours(3)->toDateTimeString());

    $service = new UpdateService;

    expect($service->shouldCheckForUpdates())->toBeFalse();
});

it('can mark update as available', function (): void {
    $service = new UpdateService;
    $service->markUpdateAvailable('2.0.0');

    expect($service->isUpdateAvailable())->toBeTrue()
        ->and($service->getUpdateVersion())->toBe('2.0.0');
});

it('can clear update availability', function (): void {
    $service = new UpdateService;
    $service->markUpdateAvailable('2.0.0');
    $service->clearUpdateAvailable();

    expect($service->isUpdateAvailable())->toBeFalse()
        ->and($service->getUpdateVersion())->toBeNull();
});

it('returns current version from config', function (): void {
    config(['nativephp.version' => '1.5.0']);

    $service = new UpdateService;

    expect($service->getCurrentVersion())->toBe('1.5.0');
});

it('returns last check time as carbon instance', function (): void {
    $checkTime = now()->subHours(2);
    Setting::set('last_update_check', $checkTime->toDateTimeString());

    $service = new UpdateService;
    $lastCheck = $service->getLastCheckTime();

    expect($lastCheck)->toBeInstanceOf(Carbon::class)
        ->and($lastCheck->diffInMinutes($checkTime))->toBeLessThan(1);
});

it('returns null when no last check time', function (): void {
    $service = new UpdateService;

    expect($service->getLastCheckTime())->toBeNull();
});
