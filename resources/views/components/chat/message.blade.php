@props(['role' => 'assistant', 'content' => '', 'time' => null, 'attachments' => []])

<div class="chat-row {{ $role === 'user' ? 'user flex-col items-end' : '' }}">
    @if ($role === 'assistant')
        <x-chat.avatar type="ai" />
    @endif

    <div class="space-y-2 {{ $role === 'user' ? 'w-full max-w-2xl' : 'flex-1' }}">
        <x-chat.bubble
            :type="$role"
            :content="$content"
        >
            {{ $slot }}
        </x-chat.bubble>

        @if (count($attachments) > 0)
            <x-chat.attachments :attachments="$attachments" />
        @endif

        @if ($time && config('purrai.ui.show_timestamps', false) && $role === 'assistant')
            <div
                class="message-timestamp"
                x-data="{ showFull: false }"
                @mouseenter="showFull = true"
                @mouseleave="showFull = false"
            >
                <span
                    x-show="!showFull"
                    x-transition:enter.duration.500ms
                >{{ $time->diffForHumans() }}</span>
                <span
                    x-show="showFull"
                    x-transition
                    x-cloak
                >{{ $time->format(__('chat.date_format_full')) }}</span>
            </div>
        @endif
    </div>

    @if ($role === 'user')
        <div class="flex justify-end gap-2 items-center select-none">
            @if ($time && config('purrai.ui.show_timestamps', false))
                <div
                    class="message-timestamp text-right"
                    x-data="{ showFull: false }"
                    @mouseenter="showFull = true"
                    @mouseleave="showFull = false"
                >
                    <span
                        x-show="!showFull"
                        x-transition:enter.duration.500ms
                    >{{ $time->diffForHumans() }}</span>
                    <span
                        x-show="showFull"
                        x-transition
                        x-cloak
                    >{{ $time->format(__('chat.date_format_full')) }}</span>
                </div>
            @endif
            <x-chat.avatar type="user" />
        </div>
    @endif
</div>
