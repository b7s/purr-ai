@props(['supportedTypes' => []])

@php
    $hasImage = in_array('image', $supportedTypes);
    $hasDocument = in_array('document', $supportedTypes);
    $hasAudio = in_array('audio', $supportedTypes);
    $hasVideo = in_array('video', $supportedTypes);
    $hasAnySupport = !empty($supportedTypes);
@endphp

<div
    class="attachment-selector-container"
    x-data="{
        open: false,
        supportedTypes: @js($supportedTypes),
        hasSupport: @js($hasAnySupport),
        selectFile(type) {
            const input = this.$refs['fileInput_' + type];
            if (input) {
                input.click();
            }
            this.open = false;
        }
    }"
>
    <div
        class="relative"
        @click.away="open = false"
    >
        <x-ui.form.button
            type="button"
            variant="ghost"
            icon="plus"
            :title="__('ui.tooltips.attach_file')"
            @click="hasSupport ? open = !open : null"
            ::class="{ 'opacity-50 cursor-not-allowed': !hasSupport }"
        />

        {{-- Attachment Type Dropdown --}}
        <div
            x-show="open"
            x-transition
            x-cloak
            class="attachment-selector-dropdown purrai-opacity-box"
        >
            @if ($hasImage)
                <button
                    type="button"
                    @click="selectFile('image')"
                    class="attachment-selector-option"
                >
                    <i class="iconoir-media-image text-lg"></i>
                    <span>{{ __('chat.attachments.image') }}</span>
                </button>
            @endif

            @if ($hasDocument)
                <button
                    type="button"
                    @click="selectFile('document')"
                    class="attachment-selector-option"
                >
                    <i class="iconoir-page text-lg"></i>
                    <span>{{ __('chat.attachments.document') }}</span>
                </button>
            @endif

            @if ($hasAudio)
                <button
                    type="button"
                    @click="selectFile('audio')"
                    class="attachment-selector-option"
                >
                    <i class="iconoir-music-double-note text-lg"></i>
                    <span>{{ __('chat.attachments.audio') }}</span>
                </button>
            @endif

            @if ($hasVideo)
                <button
                    type="button"
                    @click="selectFile('video')"
                    class="attachment-selector-option"
                >
                    <i class="iconoir-video-camera text-lg"></i>
                    <span>{{ __('chat.attachments.video') }}</span>
                </button>
            @endif

            @if (!$hasAnySupport)
                <div class="attachment-selector-empty">
                    <i class="iconoir-warning-triangle text-lg"></i>
                    <span>{{ __('chat.attachments.no_support') }}</span>
                </div>
            @endif
        </div>

        {{-- Hidden File Inputs --}}
        @if ($hasImage)
            <input
                type="file"
                x-ref="fileInput_image"
                wire:model="pendingFiles"
                accept="image/jpeg,image/png,image/gif,image/webp"
                multiple
                class="hidden"
            >
        @endif

        @if ($hasDocument)
            <input
                type="file"
                x-ref="fileInput_document"
                wire:model="pendingFiles"
                accept=".pdf,.doc,.docx,.txt,.md,.csv,.xls,.xlsx"
                multiple
                class="hidden"
            >
        @endif

        @if ($hasAudio)
            <input
                type="file"
                x-ref="fileInput_audio"
                wire:model="pendingFiles"
                accept="audio/mpeg,audio/wav,audio/ogg,audio/mp3,audio/m4a"
                multiple
                class="hidden"
            >
        @endif

        @if ($hasVideo)
            <input
                type="file"
                x-ref="fileInput_video"
                wire:model="pendingFiles"
                accept="video/mp4,video/webm,video/ogg,video/quicktime"
                multiple
                class="hidden"
            >
        @endif
    </div>
</div>
