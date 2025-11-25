<?php

declare(strict_types=1);

use App\Livewire\Chat;
use App\Models\Conversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

it('displays conversation history with pagination', function () {
    Conversation::factory()->count(15)->create();

    Livewire::test(Chat::class)
        ->assertSet('conversations', function (array $conversations) {
            return \count($conversations) === 10;
        });
});

it('loads all conversations ordered by updated_at desc', function () {
    $old = Conversation::factory()->create(['updated_at' => now()->subDays(2)]);
    $recent = Conversation::factory()->create(['updated_at' => now()]);

    Livewire::test(Chat::class)
        ->assertSet('conversations', function (array $conversations) use ($recent, $old) {
            return $conversations[0]['id'] === $recent->id
                && $conversations[1]['id'] === $old->id;
        });
});

it('can load a conversation from history', function () {
    $conversation = Conversation::factory()->create(['title' => 'Test Conversation']);

    Livewire::test(Chat::class)
        ->call('loadConversation', $conversation->id)
        ->assertRedirect(route('chat', ['conversationId' => $conversation->id]));
});

it('can edit conversation title via saveTitleDirect', function () {
    $conversation = Conversation::factory()->create(['title' => 'Old Title']);

    Livewire::test(Chat::class)
        ->call('saveTitleDirect', $conversation->id, 'New Title');

    assertDatabaseHas('conversations', [
        'id' => $conversation->id,
        'title' => 'New Title',
    ]);
});

it('validates title when editing via saveTitleDirect', function () {
    $conversation = Conversation::factory()->create(['title' => 'Original']);

    Livewire::test(Chat::class)
        ->call('saveTitleDirect', $conversation->id, '');

    assertDatabaseHas('conversations', [
        'id' => $conversation->id,
        'title' => 'Original',
    ]);
});

it('paginates conversations correctly', function () {
    Conversation::factory()->count(15)->create();

    Livewire::test(Chat::class)
        ->assertViewHas('hasMorePages', true);
});

it('respects configured conversations per page limit', function () {
    $limit = config('purrai.limits.conversations_per_page');
    Conversation::factory()->count($limit + 5)->create();

    Livewire::test(Chat::class)
        ->assertSet('conversations', function (array $conversations) use ($limit) {
            return \count($conversations) === $limit;
        });
});
