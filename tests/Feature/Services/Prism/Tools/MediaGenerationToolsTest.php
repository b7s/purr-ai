<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Services\Prism\Tools\AudioGenerationTool;
use App\Services\Prism\Tools\ImageGenerationTool;
use App\Services\Prism\Tools\VideoGenerationTool;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('tools');

test('image generation tool returns null when no model is configured', function () {
    Setting::set('openai_config', json_encode([]));

    $tool = ImageGenerationTool::make();

    expect($tool)->toBeNull();
});

test('audio generation tool returns null when no model is configured', function () {
    Setting::set('openai_config', json_encode([]));

    $tool = AudioGenerationTool::make();

    expect($tool)->toBeNull();
});

test('video generation tool returns null when no model is configured', function () {
    Setting::set('openai_config', json_encode([]));

    $tool = VideoGenerationTool::make();

    expect($tool)->toBeNull();
});

test('image generation tool is created when model is configured', function () {
    Setting::setJsonEncrypted('openai_config', [
        'key' => 'test-key',
        'models_image' => ['dall-e-3'],
    ]);

    $tool = ImageGenerationTool::make();

    expect($tool)->not->toBeNull()
        ->and($tool->name())->toBe('generate_image');
});

test('audio generation tool is created when model is configured', function () {
    Setting::setJsonEncrypted('openai_config', [
        'key' => 'test-key',
        'models_audio' => ['tts-1'],
    ]);

    $tool = AudioGenerationTool::make();

    expect($tool)->not->toBeNull()
        ->and($tool->name())->toBe('generate_audio');
});

test('video generation tool is created when model is configured', function () {
    Setting::setJsonEncrypted('openai_config', [
        'key' => 'test-key',
        'models_video' => ['video-model'],
    ]);

    $tool = VideoGenerationTool::make();

    expect($tool)->not->toBeNull()
        ->and($tool->name())->toBe('generate_video');
});
