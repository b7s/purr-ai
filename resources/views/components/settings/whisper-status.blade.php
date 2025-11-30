@props(['status' => [], 'isDownloading' => false, 'progress' => '', 'error' => null])

@php
    $isReady = ($status['binary'] ?? false) && ($status['model'] ?? false) && ($status['ffmpeg'] ?? false);
@endphp

@if (!$isReady)
    <div>
        <p class="whisper-alert-description">
            {{ __('settings.other.speech_recognition_description') }}
        </p>

        <ul class="whisper-status-list">
            <li class="whisper-status-item">
                <i class="iconoir-{{ $status['ffmpeg'] ?? false ? 'check-circle' : 'xmark-circle' }} whisper-status-icon {{ $status['ffmpeg'] ?? false ? 'success' : 'error' }}"></i>
                <span class="whisper-status-text {{ $status['ffmpeg'] ?? false ? 'success' : 'error' }}">
                    FFmpeg
                </span>
            </li>
            <li class="whisper-status-item">
                <i class="iconoir-{{ $status['binary'] ?? false ? 'check-circle' : 'xmark-circle' }} whisper-status-icon {{ $status['binary'] ?? false ? 'success' : 'error' }}"></i>
                <span class="whisper-status-text {{ $status['binary'] ?? false ? 'success' : 'error' }}">
                    Whisper {{ __('settings.other.binary') }}
                </span>
            </li>
            <li class="whisper-status-item">
                <i class="iconoir-{{ $status['model'] ?? false ? 'check-circle' : 'xmark-circle' }} whisper-status-icon {{ $status['model'] ?? false ? 'success' : 'error' }}"></i>
                <span class="whisper-status-text {{ $status['model'] ?? false ? 'success' : 'error' }}">
                    Whisper {{ __('settings.other.model') }}
                </span>
            </li>
            @if ($status['gpu'] ?? false)
                <li class="whisper-status-item">
                    <i class="iconoir-check-circle whisper-status-icon success"></i>
                    <span class="whisper-status-text success">
                        GPU {{ __('settings.other.acceleration') }}
                    </span>
                </li>
            @endif
        </ul>

        @if ($progress && $isDownloading)
            <div class="whisper-progress">
                <x-ui.loading-icon />
                <span>{{ $progress }}</span>
            </div>
        @endif

        @if ($error)
            <div class="whisper-error">
                <div class="whisper-error-header">
                    <i class="iconoir-warning-triangle"></i>
                    <span>{{ __('settings.other.download_failed') }}</span>
                </div>
                <div class="whisper-error-content">
                    {{ $error }}
                </div>
            </div>
        @endif

        @if (!($status['binary'] ?? false) || !($status['model'] ?? false) || !($status['ffmpeg'] ?? false))
            <button
                type="button"
                wire:click="downloadWhisper"
                wire:loading.attr="disabled"
                wire:target="downloadWhisper"
                class="whisper-download-btn"
            >
                <span
                    wire:loading.remove
                    wire:target="downloadWhisper"
                >
                    <i class="iconoir-download mr-1.5"></i>
                    {{ __('settings.other.download_whisper') }}
                </span>
                <span
                    wire:loading
                    wire:target="downloadWhisper"
                    class="flex items-center gap-2"
                >
                    <x-ui.loading-icon />
                    {{ __('settings.other.downloading') }}
                </span>
            </button>
        @endif
    </div>
@else
    <div class="whisper-ready">
        <div class="whisper-ready-content">
            <i class="iconoir-check-circle whisper-ready-icon"></i>
            <div>
                <span class="whisper-ready-text">
                    {{ __('settings.other.speech_recognition_ready') }}
                </span>
                @if ($status['gpu'] ?? false)
                    <span class="whisper-gpu-badge">
                        GPU
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif
