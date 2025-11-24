<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chat extends Component
{
    use WithFileUploads;

    public ?int $conversationId = null;

    public string $message = '';

    public array $attachments = [];

    public function mount(?int $conversationId = null): void
    {
        $this->conversationId = $conversationId;
    }

    public function newConversation(): void
    {
        $this->conversationId = null;
        $this->message = '';
    }

    public function loadConversation(int $conversationId): void
    {
        $this->conversationId = $conversationId;
    }

    public function sendMessage(): void
    {
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

        $this->message = '';
        $this->dispatch('message-sent');
    }

    public function render(): mixed
    {
        $conversation = $this->conversationId
            ? Conversation::with('messages.attachments')->find($this->conversationId)
            : null;

        return view('livewire.chat', [
            'conversation' => $conversation,
        ])->layout('components.layouts.app');
    }
}
