<?php

declare(strict_types=1);

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('settings page can be accessed', function () {
    $response = $this->get('/settings');

    $response->assertSuccessful();
    $response->assertSeeLivewire('settings');
});

test('settings can be saved', function () {
    Setting::set('mascot_name', 'TestCat');

    expect(Setting::get('mascot_name'))->toBe('TestCat');
});

test('encrypted settings can be saved and retrieved', function () {
    $apiKey = 'sk-test-key-12345';

    Setting::setEncrypted('test_key', $apiKey);

    $retrieved = Setting::getEncrypted('test_key');

    expect($retrieved)->toBe($apiKey);
});

test('window opacity api returns correct value', function () {
    Setting::set('window_opacity', 75);

    $response = $this->get('/api/settings/window-opacity');

    $response->assertSuccessful();
    $response->assertJson(['opacity' => 75]);
});
