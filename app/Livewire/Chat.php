<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageDraft;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Chat extends Component
{
    use WithFileUploads, WithPagination;

    public ?int $conversationId = null;

    public string $message = '';

    public array $attachments = [];

    public ?int $editingConversationId = null;

    public string $editingTitle = '';

    public function mount(?int $conversationId = null): void
    {
        $this->conversationId = $conversationId;

        $this->loadDraft();

        $this->dispatch('reset-window-state');
    }

    public function newConversation(): void
    {
        $this->clearDraft();
        $this->conversationId = null;
        $this->message = '';
    }

    public function loadConversation(int $conversationId): void
    {
        $this->conversationId = $conversationId;
        $this->loadDraft();
        $this->redirect(route('chat', ['conversationId' => $conversationId]));
    }

    public function saveTitleDirect(int $conversationId, string $title): bool
    {
        try {
            $title = trim($title);

            if (empty($title) || strlen($title) > 255) {
                return false;
            }

            $conversation = Conversation::find($conversationId);

            if (! $conversation) {
                return false;
            }

            $conversation->timestamps = false;
            $conversation->update(['title' => $title]);
            $conversation->timestamps = true;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function loadMore(): void
    {
        $this->dispatch('load-more');
    }

    public function saveDraft(): void
    {
        $content = trim($this->message);

        if (empty($content)) {
            $this->clearDraft();

            return;
        }

        MessageDraft::updateOrCreate(
            ['conversation_id' => $this->conversationId],
            ['content' => $content]
        );
    }

    public function sendMessage(): void
    {
        $this->message = trim($this->message);

        $this->validate([
            'message' => 'required|string|max:'.config('purrai.limits.max_message_length'),
        ]);

        if (! $this->conversationId) {
            $conversation = Conversation::create([
                'title' => substr($this->message, 0, 50),
            ]);
            $this->conversationId = $conversation->id;
        }

        Message::create([
            'conversation_id' => $this->conversationId,
            'role' => 'user',
            'content' => $this->message,
        ]);

        $this->clearDraft();
        $this->message = '';
        $this->dispatch('message-sent');
    }

    public function render(): mixed
    {
        $conversation = $this->conversationId
            ? Conversation::with('messages.attachments')->find($this->conversationId)
            : null;

        $conversations = Conversation::query()
            ->selectRaw('id, SUBSTR(title, 1, 60) as title, created_at, updated_at')
            ->orderBy('updated_at', 'desc')
            ->paginate(config('purrai.limits.conversations_per_page'));

        return view('livewire.chat', [
            'conversation' => $conversation,
            'conversations' => $conversations,
        ]);
    }

    private function loadDraft(): void
    {
        $draft = MessageDraft::where('conversation_id', $this->conversationId)->first();

        if ($draft) {
            $this->message = $draft->content;
        }
    }

    private function clearDraft(): void
    {
        MessageDraft::where('conversation_id', $this->conversationId)->delete();
    }
}
