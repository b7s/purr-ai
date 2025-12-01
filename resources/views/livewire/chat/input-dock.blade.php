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
                    if (!textarea || !container) {
                        return;
                    }
                    textarea.style.height = 'auto';
                    const newHeight = Math.min(textarea.scrollHeight, this.maxHeight);
                    textarea.style.height = newHeight + 'px';
                    container.style.height = newHeight + 'px';
                    textarea.style.overflowY = textarea.scrollHeight > this.maxHeight ? 'auto' : 'hidden';
                },
                syncValue() {
                    const textarea = $refs.messageInput;
                    if (!textarea) {
                        return;
                    }
                    $wire.set('message', textarea.value);
                    this.adjustHeight();
                },
                async handlePaste(event) {
                    const items = event.clipboardData?.items;
                    if (!items) {
                        return;
                    }

                    const files = [];
                    for (let i = 0; i < items.length; i++) {
                        const item = items[i];
                        if (item.kind === 'file') {
                            const file = item.getAsFile();
                            if (file) {
                                files.push(file);
                            }
                        }
                    }

                    if (files.length > 0) {
                        event.preventDefault();

                        $wire.uploadMultiple('pendingFiles', files, () => {
                            console.log('Files uploaded successfully');
                        }, (error) => {
                            console.error('Upload error:', error);
                        });
                    }
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
                @paste="handlePaste($event)"
                placeholder="{{ __('chat.placeholder') }}"
                rows="1"
                maxlength="{{ config('purrai.limits.max_message_length') }}"
                class="input-field"
                @keydown.ctrl.enter="$wire.sendMessage()"
            ></textarea>
        </div>

        <div class="flex gap-1">
            <x-ui.form.button
                type="button"
                variant="ghost"
                icon="microphone"
                :title="__('ui.tooltips.record_audio')"
                id="audio_device-button"
            />
            <x-ui.form.button
                type="submit"
                variant="primary"
                :title="__('ui.tooltips.send_message')"
                id="send-message-btn"
            >
                <i class="iconoir-arrow-up text-xl font-bold stroke-[3px]"></i>
            </x-ui.form.button>
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
