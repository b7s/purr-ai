@props([
    'label' => null,
    'description' => null,
    'options' => [],
    'model' => null,
    'columns' => null,
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
    
    <div @class([
        'flex gap-3' => !$columns,
        'grid gap-3' => $columns,
        "grid-cols-{$columns}" => $columns,
    ])>
        @foreach($options as $value => $option)
            <label class="settings-radio-card {{ $option['height'] ?? '' }}">
                <input 
                    type="radio" 
                    @if($model) wire:model.live="{{ $model }}" @endif
                    value="{{ $value }}" 
                    class="sr-only"
                >
                <span class="settings-radio-label {{ $option['class'] ?? '' }}">
                    @if(isset($option['icon']))
                        <i class="iconoir-{{ $option['icon'] }} {{ $option['iconClass'] ?? 'text-xl' }}"></i>
                    @endif
                    
                    @if(isset($option['label']))
                        <span class="{{ $option['labelClass'] ?? '' }}">{{ $option['label'] }}</span>
                    @endif
                    
                    @if(isset($option['description']))
                        <span class="{{ $option['descriptionClass'] ?? 'text-xs opacity-70 text-center leading-tight' }}">
                            {{ $option['description'] }}
                        </span>
                    @endif
                </span>
            </label>
        @endforeach
    </div>
</div>
