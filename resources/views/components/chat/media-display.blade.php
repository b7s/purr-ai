@props(['media' => []])

@php
    $mediaItems = is_string($media) ? json_decode($media, true) : $media;
    if (!is_array($mediaItems)) {
        $mediaItems = [];
    }
@endphp

@if (!empty($mediaItems))
    <div class="media-display-container">
        @foreach ($mediaItems as $item)
            @php
                $type = $item['type'] ?? 'image';
                $url = $item['url'] ?? '';
                $revisedPrompt = $item['revised_prompt'] ?? null;
            @endphp

            @if ($type === 'image')
                <div class="media-item media-image">
                    <div class="media-wrapper">
                        <img
                            src="{{ $url }}"
                            alt="{{ $revisedPrompt ?? 'Generated image' }}"
                            class="media-content"
                            loading="lazy"
                            @click="$dispatch('open-media-modal', { url: '{{ $url }}', type: 'image' })"
                        />
                    </div>
                    <div class="media-actions">
                        <x-ui.download-button
                            :url="$url"
                            filename="generated-image.png"
                            class="media-download-btn"
                        />
                    </div>
                    @if ($revisedPrompt)
                        <p class="media-caption">{{ $revisedPrompt }}</p>
                    @endif
                </div>
            @elseif ($type === 'video')
                <div class="media-item media-video">
                    <div class="media-wrapper">
                        <video
                            src="{{ $url }}"
                            controls
                            class="media-content"
                        ></video>
                    </div>
                    <div class="media-actions">
                        <x-ui.download-button
                            :url="$url"
                            filename="generated-video.mp4"
                            class="media-download-btn"
                        />
                    </div>
                </div>
            @elseif ($type === 'audio')
                <div class="media-item media-audio">
                    <div class="media-wrapper">
                        <audio
                            src="{{ $url }}"
                            controls
                            class="media-content"
                        ></audio>
                    </div>
                    <div class="media-actions">
                        <x-ui.download-button
                            :url="$url"
                            filename="generated-audio.mp3"
                            class="media-download-btn"
                        />
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
