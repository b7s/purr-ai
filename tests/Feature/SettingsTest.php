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

test('json settings can be saved and retrieved', function () {
    $config = ['key' => 'test-key', 'models' => ['model1', 'model2']];

    Setting::setJson('test_config', $config);

    $retrieved = Setting::getJson('test_config');

    expect($retrieved)->toBe($config);
});
