<?php

declare(strict_types=1);

use App\Livewire\Chat;
use App\Models\Attachment;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses()->group('chat', 'attachments');

beforeEach(function () {
    $this->artisan('migrate:fresh');
    Storage::fake('local');
});

it('can remove attachment from pending list', function () {
    $component = Livewire::test(Chat::class)
        ->set('selectedModel', 'google:gemini-pro')
        ->set('pendingAttachments', [
            ['name' => 'test.jpg', 'type' => 'image', 'size' => 1000, 'preview' => null, 'tempPath' => '/tmp/test.jpg', 'mimeType' => 'image/jpeg'],
        ])
        ->set('pendingFiles', [])
        ->assertCount('pendingAttachments', 1)
        ->call('removeAttachment', 0)
        ->assertCount('pendingAttachments', 0);
});

it('can clear all attachments', function () {
    Livewire::test(Chat::class)
        ->set('selectedModel', 'google:gemini-pro')
        ->set('pendingAttachments', [
            ['name' => 'test1.jpg', 'type' => 'image', 'size' => 1000, 'preview' => null, 'tempPath' => '/tmp/test1.jpg', 'mimeType' => 'image/jpeg'],
            ['name' => 'test2.jpg', 'type' => 'image', 'size' => 1000, 'preview' => null, 'tempPath' => '/tmp/test2.jpg', 'mimeType' => 'image/jpeg'],
        ])
        ->assertCount('pendingAttachments', 2)
        ->call('clearAttachments')
        ->assertCount('pendingAttachments', 0);
});

it('returns empty array for supported media types when no model selected', function () {
    $component = Livewire::test(Chat::class)
        ->set('selectedModel', '');

    $result = $component->instance()->getSupportedMediaTypes();

    expect($result)->toBeArray()->toBeEmpty();
});

it('returns supported media types for google provider', function () {
    $component = Livewire::test(Chat::class)
        ->set('selectedModel', 'google:gemini-pro');

    $result = $component->instance()->getSupportedMediaTypes();

    expect($result)->toBeArray()
        ->toContain('image')
        ->toContain('audio')
        ->toContain('video')
        ->toContain('document');
});

it('returns supported media types for openai provider', function () {
    $component = Livewire::test(Chat::class)
        ->set('selectedModel', 'openai:gpt-4');

    $result = $component->instance()->getSupportedMediaTypes();

    expect($result)->toBeArray()
        ->toContain('image')
        ->not->toContain('audio')
        ->not->toContain('video');
});

it('returns supported media types for anthropic provider', function () {
    $component = Livewire::test(Chat::class)
        ->set('selectedModel', 'anthropic:claude-3');

    $result = $component->instance()->getSupportedMediaTypes();

    expect($result)->toBeArray()
        ->toContain('image')
        ->toContain('document')
        ->not->toContain('audio')
        ->not->toContain('video');
});

it('can send message with text only', function () {
    Livewire::test(Chat::class)
        ->set('selectedModel', 'openai:gpt-4')
        ->set('message', 'Hello, PurrAI!')
        ->call('sendMessage')
        ->assertSet('message', '');

    assertDatabaseHas('messages', [
        'content' => 'Hello, PurrAI!',
        'role' => 'user',
    ]);
});

it('clears pending attachments after sending message', function () {
    Livewire::test(Chat::class)
        ->set('selectedModel', 'openai:gpt-4')
        ->set('pendingAttachments', [
            ['name' => 'test.jpg', 'type' => 'image', 'size' => 1000, 'preview' => null, 'tempPath' => '/tmp/test.jpg', 'mimeType' => 'image/jpeg'],
        ])
        ->set('message', 'Check this image')
        ->call('sendMessage')
        ->assertSet('message', '')
        ->assertCount('pendingAttachments', 0);
});

it('creates conversation with attachment title when no message text', function () {
    Livewire::test(Chat::class)
        ->set('selectedModel', 'openai:gpt-4')
        ->set('pendingAttachments', [
            ['name' => 'test.jpg', 'type' => 'image', 'size' => 1000, 'preview' => null, 'tempPath' => '/tmp/test.jpg', 'mimeType' => 'image/jpeg'],
        ])
        ->set('message', '')
        ->call('sendMessage');

    assertDatabaseHas('conversations', [
        'title' => __('chat.attachment_conversation'),
    ]);
});

it('saves attachments to database when message is sent', function () {
    $file = UploadedFile::fake()->image('test-image.jpg', 100, 100);
    $tempPath = $file->store('livewire-tmp', 'local');

    $conversation = Conversation::factory()->create();
    $message = Message::factory()->create([
        'conversation_id' => $conversation->id,
        'role' => 'user',
        'content' => 'Test with attachment',
    ]);

    Attachment::create([
        'message_id' => $message->id,
        'type' => 'image',
        'filename' => 'test-image.jpg',
        'path' => $tempPath,
        'mime_type' => 'image/jpeg',
        'size' => $file->getSize(),
    ]);

    assertDatabaseHas('attachments', [
        'message_id' => $message->id,
        'type' => 'image',
        'filename' => 'test-image.jpg',
    ]);
});

it('detects supported media types from prism mappers', function () {
    $providerConfig = app(\App\Services\Prism\ProviderConfig::class);

    // OpenAI supports image and document
    $openaiTypes = $providerConfig->getSupportedMediaTypes('openai');
    expect($openaiTypes)->toContain('image')->toContain('document');

    // Anthropic supports image and document
    $anthropicTypes = $providerConfig->getSupportedMediaTypes('anthropic');
    expect($anthropicTypes)->toContain('image')->toContain('document');

    // Google (Gemini) supports all types
    $googleTypes = $providerConfig->getSupportedMediaTypes('google');
    expect($googleTypes)->toContain('image')
        ->toContain('document')
        ->toContain('audio')
        ->toContain('video');

    // XAI supports only image
    $xaiTypes = $providerConfig->getSupportedMediaTypes('xai');
    expect($xaiTypes)->toContain('image')
        ->not->toContain('document')
        ->not->toContain('audio')
        ->not->toContain('video');

    // Ollama supports only image
    $ollamaTypes = $providerConfig->getSupportedMediaTypes('ollama');
    expect($ollamaTypes)->toContain('image')
        ->not->toContain('document');
});
