@props(['attachments' => []])

@if (count($attachments) > 0)
    <div class="attachment-preview-container">
        <div class="attachment-preview-list">
            @foreach ($attachments as $index => $attachment)
                <div
                    class="attachment-badge"
                    wire:key="attachment-{{ $index }}"
                >
                    @if ($attachment['type'] === 'image' && !empty($attachment['preview']))
                        <img
                            src="{{ $attachment['preview'] }}"
                            alt="{{ $attachment['name'] }}"
                            class="attachment-badge-preview"
                        >
                    @else
                        <span class="attachment-badge-icon">
                            @switch($attachment['type'])
                                @case('image')
                                    <i class="iconoir-media-image"></i>
                                @break

                                @case('audio')
                                    <i class="iconoir-music-double-note"></i>
                                @break

                                @case('video')
                                    <i class="iconoir-video-camera"></i>
                                @break

                                @default
                                    <i class="iconoir-page"></i>
                            @endswitch
                        </span>
                    @endif
                    <span class="attachment-badge-name">{{ Str::limit($attachment['name'], 20) }}</span>
                    <button
                        type="button"
                        wire:click="removeAttachment({{ $index }})"
                        class="attachment-badge-remove"
                        title="{{ __('chat.attachments.remove') }}"
                    >
                        <i class="iconoir-xmark"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endif
