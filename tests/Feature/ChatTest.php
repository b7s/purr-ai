<?php

declare(strict_types=1);

use App\Livewire\Chat;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses()->group('chat');

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

it('renders chat component', function () {
    Livewire::test(Chat::class)
        ->assertStatus(200)
        ->assertSee('Welcome to');
});

it('can send a message', function () {
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

it('creates conversation on first message', function () {
    Livewire::test(Chat::class)
        ->set('selectedModel', 'openai:gpt-4')
        ->set('message', 'First message')
        ->call('sendMessage');

    assertDatabaseHas('conversations', [
        'title' => 'First message',
    ]);
});

it('validates model is selected', function () {
    Livewire::test(Chat::class)
        ->set('selectedModel', '')
        ->set('message', 'Hello')
        ->call('sendMessage')
        ->assertHasErrors(['message']);
});

it('can start new conversation', function () {
    $conversation = Conversation::factory()->create();

    Livewire::test(Chat::class, ['conversationId' => $conversation->id])
        ->call('newConversation')
        ->assertSet('conversationId', null)
        ->assertSet('message', '');
});

it('displays messages from conversation', function () {
    $conversation = Conversation::factory()->create();
    $message = Message::factory()->create([
        'conversation_id' => $conversation->id,
        'content' => 'Test message content',
        'role' => 'user',
    ]);

    Livewire::test(Chat::class, ['conversationId' => $conversation->id])
        ->assertSee('Test message content');
});

it('validates message is required', function () {
    Livewire::test(Chat::class)
        ->set('message', '')
        ->call('sendMessage')
        ->assertHasErrors(['message' => 'required']);
});

it('validates message max length', function () {
    $longMessage = str_repeat('a', config('purrai.limits.max_message_length') + 1);

    Livewire::test(Chat::class)
        ->set('message', $longMessage)
        ->call('sendMessage')
        ->assertHasErrors(['message' => 'max']);
});
