@props([
    'label' => null,
    'description' => null,
    'helpText' => null,
    'model' => null,
    'min' => 0,
    'max' => 100,
    'value' => 0,
    'suffix' => '',
])

<div class="card">
    @if($label)
        <label class="settings-label">
            {{ $label }}
        </label>
    @endif
    
    @if($description)
        <p class="settings-description">
            {{ $description }}
        </p>
    @endif
    
    <div class="flex items-center gap-4">
        <input 
            type="range" 
            @if($model) wire:model.live.debounce.300ms="{{ $model }}" @endif
            min="{{ $min }}" 
            max="{{ $max }}"
            {{ $attributes->merge(['class' => 'settings-slider']) }}
        >
        <span class="settings-value">
            {{ $value }}{{ $suffix }}
        </span>
    </div>
    
    @if($helpText)
        <p class="help-text">
            {{ $helpText }}
        </p>
    @endif
    
    {{ $slot }}
</div>
