@props([
    'label' => null,
    'description' => null,
    'helpText' => null,
    'type' => 'text',
    'model' => null,
])

<div class="input">
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
    
    <input 
        type="{{ $type }}" 
        @if($model) wire:model.blur="{{ $model }}" @endif
        {{ $attributes->merge(['class' => 'settings-input']) }}
    >
    
    @if($helpText)
        <p class="help-text">
            {{ $helpText }}
        </p>
    @endif
</div>
