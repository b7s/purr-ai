<x-slot name="headerActions">
    <div
        x-data="historyDropdown(@js($conversations), {{ $hasMorePages ? 'true' : 'false' }}, @js($searchQuery))"
        class="flex items-center gap-2"
    >
        <span
            @click="startNewConversation()"
            x-transition
        >
            <x-ui.icon-button
                icon="plus"
                :title="__('ui.tooltips.new_chat')"
            />
        </span>

        <div class="relative">
            <x-ui.icon-button
                @click="open = !open"
                icon="clock-rotate-right"
                :title="__('ui.tooltips.history')"
            />

            {{-- History Dropdown/Modal --}}
            <div
                x-show="open"
                x-transition
                @click.away="open = false"
                @keydown.window.escape="open ? open = false : null"
                class="history-dropdown purrai-opacity-box"
            >
                {{-- Header --}}
                <div class="history-mobile-header flex items-center justify-between gap-3">
                    <h3 class="history-mobile-title flex-1">
                        {{ __('chat.history_title') }}
                    </h3>
                    <div class="history-mobile-actions flex items-center gap-2">
                        <div
                            x-show="searchOpen"
                            x-transition
                            x-cloak
                            class="history-search-field flex items-center gap-2"
                        >
                            <x-ui.input
                                type="search"
                                @keydown.escape.stop.prevent="closeSearch()"
                                autocomplete="off"
                                placeholder="{{ __('chat.search_placeholder') }}"
                                x-ref="historySearchInput"
                                x-model="searchTerm"
                                class="rounded-lg px-2 py-1"
                            ></x-ui.input>
                        </div>

                        <button
                            type="button"
                            @click="toggleSearch()"
                            :class="{ 'text-primary-500': searchOpen }"
                            class="history-mobile-btn"
                        >
                            <span class="sr-only">{{ __('chat.search_history') }}</span>
                            <i class="iconoir-search text-lg"></i>
                        </button>

                        <button
                            type="button"
                            @click="open = false"
                            class="history-mobile-btn"
                        >
                            <span class="sr-only">{{ __('ui.cancel') }}</span>
                            <i class="iconoir-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="history-list">
                    <template x-if="conversations.length === 0">
                        <div class="history-empty">
                            <i class="iconoir-chat-bubble-empty text-xl mb-4"></i>
                            <div>
                                {{ __('chat.no_conversations') }}
                            </div>
                        </div>
                    </template>

                    <template
                        x-for="conv in conversations"
                        :key="conv.id"
                    >
                        <div
                            class="history-item"
                            :data-history-item-id="conv.id"
                        >
                            <button
                                type="button"
                                @click="loadConv(conv.id)"
                                class="history-item-content"
                            >
                                <div
                                    class="history-item-title"
                                    x-text="conv.title"
                                >
                                </div>
                                <div class="history-item-meta">
                                    <span x-text="conv.created_at"></span>
                                    <span>&middot;</span>
                                    {{ __('chat.updated') }}:
                                    <span x-text="conv.updated_at_human"></span>
                                </div>
                            </button>
                            <button
                                type="button"
                                @click.stop="startEdit(conv.id, conv.title)"
                                :data-title="conv.title"
                                class="history-item-edit"
                            >
                                <i class="iconoir-edit-pencil"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <template x-if="hasMorePages">
                    <div class="history-load-more">
                        <button
                            type="button"
                            @click="loadMore()"
                            :disabled="loadingMore"
                            class="history-load-more-btn"
                            :class="{ 'opacity-50 cursor-not-allowed': loadingMore }"
                        >
                            <span x-show="!loadingMore">{{ __('chat.load_more') }}</span>
                            <span
                                x-show="loadingMore"
                                x-cloak
                            >
                                <x-ui.loading-icon />
                            </span>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Edit Title Modal --}}
            <div
                x-show="editModalOpen"
                x-transition.opacity
                @click="cancelEdit()"
                class="edit-modal-overlay"
            >
                <div
                    @click.stop
                    class="edit-modal-content"
                    x-transition
                >
                    <h3 class="edit-modal-title">
                        {{ __('chat.edit_title') }}
                    </h3>

                    <input
                        type="text"
                        x-model="editingTitle"
                        x-ref="editInput"
                        class="edit-modal-input"
                        @keydown.enter="saveEdit()"
                        @keydown.escape="cancelEdit()"
                        placeholder="{{ __('chat.title_placeholder') }}"
                    >

                    <div class="edit-modal-actions">
                        <button
                            type="button"
                            @click="cancelEdit()"
                            class="edit-modal-btn edit-modal-btn-cancel"
                        >
                            {{ __('ui.cancel') }}
                        </button>
                        <button
                            type="button"
                            @click="saveEdit()"
                            class="edit-modal-btn edit-modal-btn-confirm"
                        >
                            {{ __('ui.confirm') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-slot>

<div
    class="h-full flex flex-col"
    x-data="{
        scrollToBottom() {
                let container = document.getElementById('messages-container');
                if (container) {
                    setTimeout(() => {
                        container.scrollTo({
                            top: container.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 50);
                }
            },
            focusInput() {
                let textarea = document.querySelector('.input-field');
                if (textarea) {
                    setTimeout(() => {
                        textarea.focus();
                        this.scrollToBottom();
                    }, 150);
                }
            }
    }"
    x-init="focusInput()"
    @scroll-to-user-message.window="scrollToBottom()"
>

    {{-- Messages --}}
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

    {{-- Media Modal --}}
    <x-chat.media-modal />

    {{-- Input Dock --}}
    <div class="input-dock-wrapper">
        {{-- Model Selector --}}
        <x-chat.model-selector
            :available-models="$availableModels"
            :selected-model="$selectedModel"
        />

        {{-- Attachment Preview --}}
        <x-chat.attachment-preview :attachments="$pendingAttachments" />

        {{-- Input Form --}}
        <form
            wire:submit="sendMessage"
            class="purrai-opacity-box input-dock"
        >
            <x-chat.attachment-selector :supported-types="$this->getSupportedMediaTypes()" />

            <div
                x-data="{
                    maxHeight: 100,
                    adjustHeight() {
                        const textarea = $refs.messageInput;
                        const container = $refs.textareaContainer;
                        if (!textarea || !container) return;
                        textarea.style.height = 'auto';
                        const newHeight = Math.min(textarea.scrollHeight, this.maxHeight);
                        textarea.style.height = newHeight + 'px';
                        container.style.height = newHeight + 'px';
                        textarea.style.overflowY = textarea.scrollHeight > this.maxHeight ? 'auto' : 'hidden';
                    },
                    syncValue() {
                        const textarea = $refs.messageInput;
                        if (!textarea) return;
                        $wire.set('message', textarea.value);
                        this.adjustHeight();
                    }
                }"
                x-init="const textarea = $refs.messageInput;
                textarea.value = $wire.message || '';
                adjustHeight();
                $watch('$wire.message', (value) => {
                    if (textarea.value !== value) {
                        textarea.value = value || '';
                        $nextTick(() => adjustHeight());
                    }
                });"
                wire:ignore
                x-ref="textareaContainer"
                class="flex-1"
            >
                <textarea
                    wire:ignore
                    x-ref="messageInput"
                    @input.debounce.200ms="syncValue()"
                    @input="adjustHeight()"
                    @change="$wire.call('saveDraft')"
                    placeholder="{{ __('chat.placeholder') }}"
                    rows="1"
                    maxlength="{{ config('purrai.limits.max_message_length') }}"
                    class="input-field"
                    @keydown.ctrl.enter="$wire.sendMessage()"
                ></textarea>
            </div>

            <div class="flex gap-1">
                <x-ui.button
                    type="button"
                    variant="ghost"
                    icon="microphone"
                    :title="__('ui.tooltips.record_audio')"
                    id="audio_device-button"
                />
                <x-ui.button
                    type="submit"
                    variant="primary"
                    :title="__('ui.tooltips.send_message')"
                    id="send-message-btn"
                >
                    <i class="iconoir-arrow-up text-xl font-bold stroke-[3px]"></i>
                </x-ui.button>
            </div>

            {{-- Speech Recognition Translations --}}
            <script>
                window
                    .speechRecognitionTranslations =
                    @js([
    'settings' => __('chat.speech_recognition.settings'),
    'audio_device' => __('chat.speech_recognition.audio_device'),
    'default_audio_device' => __('chat.speech_recognition.default_audio_device'),
    'speech_provider' => __('chat.speech_recognition.speech_provider'),
    'auto_send' => __('settings.speech.auto_send'),
]);
                window
                    .noiseSuppressionLevel =
                    @js(\App\Models\Setting::get('noise_suppression_level', 'medium'));
                window
                    .useLocalSpeech =
                    @js((bool) \App\Models\Setting::get('use_local_speech', true));
                window
                    .speechProviderOptions =
                    @js(\App\Models\Setting::getSpeechProviderOptions());
                window
                    .selectedSpeechProvider =
                    @js(\App\Models\Setting::get('speech_provider', ''));
                window
                    .autoSendAfterTranscription =
                    @js((bool) \App\Models\Setting::get('auto_send_after_transcription', false));

                {{-- Chat Error Translations --}}
                window
                    .chatTranslations =
                    @js([
    'stream_error' => __('chat.errors.stream_error'),
    'try_again' => __('chat.errors.try_again'),
    'retry_message' => __('chat.errors.retry_message'),
]);
            </script>

            @error('message')
                <div class="absolute -top-8 left-4 text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="iconoir-message-alert"></i>
                    <span class="text-xs">{{ $message }}</span>
                </div>
            @enderror
        </form>
    </div>
</div>
