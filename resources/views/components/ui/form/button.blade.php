@props(['variant' => 'ghost', 'icon' => '', 'title' => '', 'type' => 'button'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => "circle-btn {$variant}"]) }} @if($title)
title="{{ $title }}" @endif>
    @if($icon)
        <i class="iconoir-{{ $icon }} text-xl"></i>
    @else
        {{ $slot }}
    @endif
</button>