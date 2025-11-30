@props(['attachments' => []])

@php
    $images = collect($attachments)->filter(fn($att) => str_starts_with($att->mime_type ?? '', 'image/'));
    $files = collect($attachments)->filter(fn($att) => !str_starts_with($att->mime_type ?? '', 'image/'));
@endphp

@if ($files->count() > 0)
    <div class="flex flex-wrap gap-2 mt-1 max-w-md">
        @foreach ($files as $file)
            <a
                href="{{ route('media.serve', ['path' => $file->path ?? '']) }}"
                download="{{ $file->filename ?? 'file' }}"
                class="file-card"
            >
                <div class="file-icon text-orange-500 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20">
                    <i class="iconoir-page text-xl"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-semibold">{{ $file->filename ?? 'File' }}</p>
                    <p class="text-[10px] opacity-60">{{ $file->size ?? '' }}</p>
                </div>
            </a>
        @endforeach
    </div>
@endif

@if ($images->count() > 0)
    <div class="attachment-grid">
        @foreach ($images->take(3) as $index => $image)
            <div
                class="attachment-thumbnail"
                @click="$dispatch('open-media-modal', { url: '{{ route('media.serve', ['path' => $image->path ?? '']) }}', type: 'image' })"
            >
                @if ($index === 2 && $images->count() > 3)
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-xs z-10 flex items-center justify-center text-white font-medium">
                        +{{ $images->count() - 2 }}
                    </div>
                @endif
                <img
                    src="{{ route('media.serve', ['path' => $image->path ?? '']) }}"
                    alt="{{ $image->filename ?? '' }}"
                >
            </div>
        @endforeach
    </div>
@endif
