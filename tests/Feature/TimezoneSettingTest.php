<?php

declare(strict_types=1);

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('saves timezone setting', function () {
    Setting::set('timezone', 'America/New_York');

    expect(Setting::get('timezone'))->toBe('America/New_York');
});

it('validates timezone on update', function () {
    $component = Livewire::test(\App\Livewire\Settings::class);

    $component->set('timezone', 'America/New_York');

    expect(Setting::get('timezone'))->toBe('America/New_York');
});

it('falls back to config timezone for invalid timezone', function () {
    $component = Livewire::test(\App\Livewire\Settings::class);

    $component->set('timezone', 'Invalid/Timezone');

    expect(Setting::get('timezone'))->toBe(config('app.timezone'));
});

it('loads timezone from settings on mount', function () {
    Setting::set('timezone', 'Europe/London');

    $component = Livewire::test(\App\Livewire\Settings::class);

    expect($component->get('timezone'))->toBe('Europe/London');
});

it('uses config timezone as default when no setting exists', function () {
    Setting::query()->where('key', 'timezone')->delete();

    $component = Livewire::test(\App\Livewire\Settings::class);

    expect($component->get('timezone'))->toBe(config('app.timezone'));
});
