@props([
    'label' => null,
    'description' => null,
    'model' => null,
    'checked' => false,
])

<div>
    <label class="flex items-center justify-between cursor-pointer">
        <div>
            @if($label)
                <span class="settings-label mb-0">
                    {{ $label }}
                </span>
            @endif
            
            @if($description)
                <p class="settings-description mt-1">
                    {{ $description }}
                </p>
            @endif
        </div>
        
        <button 
            type="button" 
            @if($model) wire:click="$toggle('{{ $model }}')" @endif
            class="settings-toggle {{ $checked ? 'active' : '' }}"
            {{ $attributes->except(['class']) }}
        >
            <span class="settings-toggle-thumb"></span>
        </button>
    </label>
</div>
