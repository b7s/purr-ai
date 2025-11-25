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
        ->assertViewHas('conversations', function ($paginatedConversations) {
            return $paginatedConversations->count() === 10;
        });
});

it('loads all conversations ordered by updated_at desc', function () {
    $old = Conversation::factory()->create(['updated_at' => now()->subDays(2)]);
    $recent = Conversation::factory()->create(['updated_at' => now()]);

    Livewire::test(Chat::class)
        ->assertViewHas('conversations', function ($conversations) use ($recent, $old) {
            return $conversations->first()->id === $recent->id
                && $conversations->last()->id === $old->id;
        });
});

it('can load a conversation from history', function () {
    $conversation = Conversation::factory()->create(['title' => 'Test Conversation']);

    Livewire::test(Chat::class)
        ->call('loadConversation', $conversation->id)
        ->assertRedirect(route('chat', ['conversationId' => $conversation->id]));
});

it('can edit conversation title', function () {
    $conversation = Conversation::factory()->create(['title' => 'Old Title']);

    Livewire::test(Chat::class)
        ->call('startEditingTitle', $conversation->id)
        ->assertSet('editingConversationId', $conversation->id)
        ->assertSet('editingTitle', '')
        ->set('editingTitle', 'New Title')
        ->call('saveTitle');

    assertDatabaseHas('conversations', [
        'id' => $conversation->id,
        'title' => 'New Title',
    ]);
});

it('can cancel editing conversation title', function () {
    $conversation = Conversation::factory()->create(['title' => 'Original Title']);

    Livewire::test(Chat::class)
        ->call('startEditingTitle', $conversation->id)
        ->assertSet('editingConversationId', $conversation->id)
        ->call('cancelEditingTitle')
        ->assertSet('editingConversationId', null)
        ->assertSet('editingTitle', '');
});

it('validates title when editing', function () {
    $conversation = Conversation::factory()->create();

    Livewire::test(Chat::class)
        ->call('startEditingTitle', $conversation->id)
        ->set('editingTitle', '')
        ->call('saveTitle')
        ->assertHasErrors(['editingTitle' => 'required']);
});

it('paginates conversations correctly', function () {
    Conversation::factory()->count(15)->create();

    Livewire::test(Chat::class)
        ->assertViewHas('conversations', function ($conversations) {
            return $conversations->hasMorePages() === true;
        });
});

it('respects configured conversations per page limit', function () {
    $limit = config('purrai.limits.conversations_per_page');
    Conversation::factory()->count($limit + 5)->create();

    Livewire::test(Chat::class)
        ->assertViewHas('conversations', function ($conversations) use ($limit) {
            return $conversations->count() === $limit;
        });
});
