@props(['type' => 'assistant', 'content' => '', 'loading' => false])

@php
    $truncateLimit = config('purrai.limits.truncate_words', 45);
    $rawText = trim($content ?: $slot);
    $isLoading = $loading || empty($rawText);
@endphp

@if ($type === 'user')
    <div
        class="chat-bubble primary prose prose-sm dark:prose-invert"
        x-data="{
            fullText: {{ json_encode($rawText) }},
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
                    return window.chatStream.parseMarkdown(this.fullText);
                }
                return this.words.slice(0, this.wordLimit).join(' ') + '...';
            }
        }"
    >
        <span
            class="whitespace-pre-wrap"
            x-html="displayText"
        ></span>
        <button
            x-show="needsTruncate"
            @click="expanded = !expanded"
            class="see-more-less-btn"
            type="button"
        >
            [
            <i class="iconoir-more-horiz-circle"></i>
            <span x-text="expanded ? '{{ __('ui.messages.see_less') }}' : '{{ __('ui.messages.see_more') }}'"></span>
            ]
        </button>
    </div>
@else
    <div
        class="chat-bubble secondary prose prose-sm dark:prose-invert max-w-full"
        x-data="{
            content: {{ json_encode($rawText) }},
            isLoading: {{ $isLoading ? 'true' : 'false' }},
            rendered: '',
            init() {
                if (this.isLoading || !this.content) {
                    this.rendered = '';
                } else if (window.chatStream && window.chatStream.parseMarkdown) {
                    this.rendered = window.chatStream.parseMarkdown(this.content);
                } else {
                    this.rendered = this.content;
                }
            }
        }"
    >
        @if ($slot->isNotEmpty())
            {{-- Streaming content slot --}}
            {{ $slot }}
            {{-- Loading indicator for streaming (hidden by JS when content arrives) --}}
            <div
                id="stream-loading-indicator"
                class="flex items-center justify-center py-2"
            >
                <x-ui.loading-icon />
            </div>
        @else
            {{-- Regular message content --}}
            <div
                x-show="!isLoading && content"
                x-html="rendered"
            ></div>
            <template x-if="isLoading || !content">
                <div class="flex items-center justify-start py-2">
                    <x-ui.loading-icon />
                </div>
            </template>
        @endif
    </div>
@endif
