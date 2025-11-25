@props(['type' => 'assistant', 'content' => ''])

@php
    $truncateLimit = config('purrai.limits.truncate_words', 45);
    $text = trim($content ?: $slot);
@endphp

@if($type === 'user')
    <div class="chat-bubble primary" x-data="{
        fullText: {{ json_encode($text) }},
        expanded: false,
        wordLimit: {{ $truncateLimit }},
        get words() {
            return this.fullText.trim().split(/\s+/);
        },
        get needsTruncate() {
            return this.words.length > this.wordLimit;
        },
        get displayText() {
            if (!this.needsTruncate || this.expanded) {
                return this.fullText;
            }
            return this.words.slice(0, this.wordLimit).join(' ') + '...';
        }
    }">
        <span class="whitespace-pre-wrap" x-text="displayText"></span>
        <button x-show="needsTruncate" @click="expanded = !expanded"
            class="text-sm opacity-70 hover:opacity-100 ml-1 cursor-pointer inline-flex items-center gap-1 select-none"
            type="button">
            [
            <i class="iconoir-more-horiz-circle"></i>
            <span x-text="expanded ? '{{ __('ui.messages.see_less') }}' : '{{ __('ui.messages.see_more') }}'"></span>
            ]
        </button>
    </div>
@else
    <div class="chat-bubble secondary whitespace-pre-wrap">
        {{ $text }}
    </div>
@endif