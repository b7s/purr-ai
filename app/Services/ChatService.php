<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageDraft;

class ChatService
{
    /**
     * Determine the initial route to open based on draft and conversation state.
     */
    public function getInitialRoute(string $type = 'chat'): string
    {
        $draft = $this->getLatestDraft();
        $lastMessage = $this->getLastMessage();

        if ($draft && $lastMessage) {
            if ($draft->updated_at->greaterThan($lastMessage->created_at)) {
                return $this->buildRoute($draft->conversation_id, $type);
            }
        }

        if ($draft && ! $lastMessage) {
            return $this->buildRoute($draft->conversation_id, $type);
        }

        $lastConversation = $this->getLastConversation();

        if ($lastConversation) {
            return $this->buildRoute($lastConversation->id, $type);
        }

        return $this->buildRoute(null, 'chat');
    }

    /**
     * Get the latest draft message.
     */
    private function getLatestDraft(): ?MessageDraft
    {
        return MessageDraft::query()
            ->latest('updated_at')
            ->first();
    }

    /**
     * Get the last message from any conversation.
     */
    private function getLastMessage(): ?Message
    {
        return Message::query()
            ->latest('created_at')
            ->first();
    }

    /**
     * Get the last updated conversation.
     */
    private function getLastConversation(): ?Conversation
    {
        return Conversation::query()
            ->latest('updated_at')
            ->first();
    }

    /**
     * Build the menubar chat route URL.
     */
    private function buildRoute(?int $conversationId, string $type = 'chat'): string
    {
        $routeName = $type === 'chat' ? 'chat' : 'menubar.chat';
        $windowId = $type === 'chat' ? config('purrai.window.main_id', 'main') : 'menubar';

        if ($conversationId) {
            return route($routeName, ['conversationId' => $conversationId, '_windowId' => $windowId]);
        }

        return route($routeName, ['_windowId' => $windowId]);
    }
}
