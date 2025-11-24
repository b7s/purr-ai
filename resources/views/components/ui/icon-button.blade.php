@props(['icon' => '', 'title' => '', 'type' => 'button'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn-icon']) }} @if($title) title="{{ $title }}" @endif>
    <i class="iconoir-{{ $icon }} text-xl"></i>
</button>