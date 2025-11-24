@props(['type' => 'assistant', 'content' => ''])

<div class="chat-bubble {{ $type === 'user' ? 'primary' : 'secondary' }}">
    {{ $content ?: $slot }}
</div>