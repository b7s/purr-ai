@props([
    'label' => null,
    'description' => null,
    'helpText' => null,
    'model' => null,
    'options' => [],
    'placeholder' => null,
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
    
    <select 
        @if($model) wire:model.live="{{ $model }}" @endif
        {{ $attributes->merge(['class' => 'settings-input']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $key => $value)
            @if(is_array($value))
                {{-- Grouped options --}}
                <optgroup label="{{ $key }}">
                    @foreach($value as $optKey => $optValue)
                        <option value="{{ $optKey }}">{{ $optValue }}</option>
                    @endforeach
                </optgroup>
            @else
                {{-- Simple options --}}
                <option value="{{ $key }}">{{ $value }}</option>
            @endif
        @endforeach
    </select>
    
    @if($helpText)
        <p class="help-text">
            {{ $helpText }}
        </p>
    @endif
</div>
