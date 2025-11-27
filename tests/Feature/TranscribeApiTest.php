<?php

declare(strict_types=1);

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

it('returns error when speech provider is not configured', function () {
    Setting::set('use_local_speech', 0);
    Setting::set('speech_provider', '');

    $response = $this->postJson('/api/transcribe', [
        'audio' => UploadedFile::fake()->create('audio.webm', 100),
    ]);

    $response->assertStatus(503)
        ->assertJson([
            'success' => false,
            'error' => 'Speech provider not configured. Please check Settings.',
        ]);
});

it('returns error when speech provider configuration is invalid', function () {
    Setting::set('use_local_speech', 0);
    Setting::set('speech_provider', 'invalid');

    $response = $this->postJson('/api/transcribe', [
        'audio' => UploadedFile::fake()->create('audio.webm', 100),
    ]);

    $response->assertStatus(503)
        ->assertJson([
            'success' => false,
            'error' => 'Invalid speech provider configuration.',
        ]);
});

it('returns error when api key is not configured', function () {
    Setting::set('use_local_speech', 0);
    Setting::set('speech_provider', 'openai:whisper-1');
    Setting::setJsonEncrypted('openai_config', ['key' => '', 'models' => []]);

    $response = $this->postJson('/api/transcribe', [
        'audio' => UploadedFile::fake()->create('audio.webm', 100),
    ]);

    $response->assertStatus(503)
        ->assertJson([
            'success' => false,
            'error' => 'API key not configured for provider. Please check Settings.',
        ]);
});

it('can get provider api key', function () {
    Setting::setJsonEncrypted('openai_config', ['key' => 'test-key-123', 'models' => []]);

    $apiKey = Setting::getProviderApiKey('openai');

    expect($apiKey)->toBe('test-key-123');
});

it('returns null for non-existent provider', function () {
    $apiKey = Setting::getProviderApiKey('non-existent');

    expect($apiKey)->toBeNull();
});
