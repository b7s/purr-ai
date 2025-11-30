@props(['url', 'filename' => null])

<a
    href="{{ $url }}"
    download="{{ $filename }}"
    class="download-btn"
    title="{{ __('ui.download') }}"
    {{ $attributes }}
>
    <i class="iconoir-download"></i>
</a>
