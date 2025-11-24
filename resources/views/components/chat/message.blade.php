@props(['role' => 'assistant', 'content' => '', 'timestamp' => null, 'attachments' => []])

<div class="chat-row {{ $role === 'user' ? 'user flex-col items-end' : '' }}">
    @if($role === 'assistant')
        <x-chat.avatar type="ai" />
    @endif

    <div class="space-y-2 {{ $role === 'user' ? 'w-full max-w-2xl' : '' }}">
        <x-chat.bubble :type="$role" :content="$content">
            {{ $slot }}
        </x-chat.bubble>

        @if(count($attachments) > 0)
            <x-chat.attachments :attachments="$attachments" />
        @endif

        @if($timestamp && config('purrai.ui.show_timestamps', false))
            <div class="message-timestamp {{ $role === 'user' ? 'text-right pr-1' : '' }}">
                {{ $timestamp }}
            </div>
        @endif
    </div>

    @if($role === 'user')
        <x-chat.avatar type="user" />
    @endif
</div>