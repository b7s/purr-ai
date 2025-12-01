<div
    class="chat-messages"
    id="messages-container"
>
    @if ($conversation && $conversation->messages->count() > 0)
        @foreach ($conversation->messages as $message)
            <x-chat.message
                :role="$message->role"
                :content="$message->content"
                :time="$message->created_at"
                :attachments="$message->attachments"
            />
        @endforeach
    @else
        <x-chat.welcome />
    @endif

    {{-- Streaming Response Container --}}
    @if ($isProcessing)
        <div class="chat-row">
            <x-chat.avatar type="ai" />
            <x-chat.bubble
                type="assistant"
                :loading="true"
            >
                <div
                    id="streaming-response"
                    class="stream-content"
                >
                </div>
            </x-chat.bubble>
        </div>
    @endif
</div>
