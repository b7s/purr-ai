<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires conversation_id parameter', function (): void {
    $response = $this->postJson('/api/chat/stream', [
        'selected_model' => 'openai:gpt-4',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['conversation_id']);
});

it('requires selected_model parameter', function (): void {
    $conversation = Conversation::factory()->create();

    $response = $this->postJson('/api/chat/stream', [
        'conversation_id' => $conversation->id,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['selected_model']);
});

it('validates conversation exists', function (): void {
    $response = $this->postJson('/api/chat/stream', [
        'conversation_id' => 99999,
        'selected_model' => 'openai:gpt-4',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['conversation_id']);
});

it('returns stream response headers', function (): void {
    $conversation = Conversation::factory()->create();
    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'role' => 'user',
        'content' => 'Hello',
    ]);

    $response = $this->post('/api/chat/stream', [
        'conversation_id' => $conversation->id,
        'selected_model' => 'openai:gpt-4',
    ]);

    $response->assertHeader('Content-Type', 'text/event-stream; charset=UTF-8');
});
