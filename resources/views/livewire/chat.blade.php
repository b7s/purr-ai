<div class="chat-container">
    <x-layouts.header>
        @if($conversation)
            <x-ui.icon-button wire:click="newConversation" icon="plus" :title="__('ui.tooltips.new_chat')" />
        @endif

        <div x-data="{ open: false }" class="relative">
            <x-ui.icon-button @click="open = !open" icon="clock" :title="__('ui.tooltips.history')" />

            <div x-show="open" @click.away="open = false" class="dropdown" style="display: none;">
                <button wire:click="loadConversation(1)" class="dropdown-item">
                    {{ __('chat.recent_conversation', ['number' => 1]) }}
                </button>
                <button wire:click="loadConversation(2)" class="dropdown-item">
                    {{ __('chat.recent_conversation', ['number' => 2]) }}
                </button>
            </div>
        </div>
    </x-layouts.header>

    {{-- Messages --}}
    <div class="chat-messages" id="messages-container">
        @if($conversation && $conversation->messages->count() > 0)
            @foreach($conversation->messages as $message)
                <x-chat.message :role="$message->role" :content="$message->content"
                    :timestamp="$message->created_at->diffForHumans()" :attachments="$message->attachments" />
            @endforeach
        @else
            <x-chat.welcome :title="__('chat.welcome_title')" :message="__('chat.welcome_message')" />
        @endif

        @if($isProcessing ?? false)
            <x-chat.loading />
        @endif
    </div>

    {{-- Input Dock --}}
    <form wire:submit="sendMessage" class="input-dock">
        <x-ui.button type="button" variant="ghost" icon="plus" :title="__('ui.tooltips.attach_file')" />

        <textarea wire:model="message" placeholder="{{ __('chat.placeholder') }}" rows="1"
            maxlength="{{ config('purrai.limits.max_message_length') }}" class="input-field" x-data
            x-on:input="$el.style.height = 'auto'; $el.style.height = ($el.scrollHeight) + 'px'"
            @keydown.ctrl.enter="$wire.sendMessage()"></textarea>

        <div class="flex gap-1">
            <x-ui.button type="button" variant="ghost" icon="microphone" :title="__('ui.tooltips.record_audio')" />
            <x-ui.button type="submit" variant="primary" :title="__('ui.tooltips.send_message')">
                <i class="iconoir-arrow-up text-xl font-bold stroke-[3px]"></i>
            </x-ui.button>
        </div>

        @error('message')
            <span class="absolute -top-8 left-4 text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </form>
</div>

@script
<script>
    // Auto-focus textarea on load
    document.addEventListener('DOMContentLoaded', () => {
        const textarea = document.querySelector('.input-field');
        if (textarea) {
            textarea.focus();
        }
    });

    // Scroll to bottom when message is sent
    $wire.on('message-sent', () => {
        const container = document.getElementById('messages-container');
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }

        // Re-focus textarea after sending
        const textarea = document.querySelector('.input-field');
        if (textarea) {
            textarea.focus();
        }
    });
</script>
@endscript