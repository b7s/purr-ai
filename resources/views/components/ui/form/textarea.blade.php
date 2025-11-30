@props([
    'label' => null,
    'description' => null,
    'helpText' => null,
    'model' => null,
    'rows' => 3,
])

<div>
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
    
    <textarea 
        rows="{{ $rows }}"
        @if($model) wire:model.blur="{{ $model }}" @endif
        {{ $attributes->merge(['class' => 'settings-input resize-none']) }}
    ></textarea>
    
    @if($helpText)
        <p class="help-text">
            {{ $helpText }}
        </p>
    @endif
</div>
