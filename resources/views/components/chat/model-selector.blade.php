@props(['availableModels' => [], 'selectedModel' => ''])

@php
    $hasModels = !empty($availableModels) && collect($availableModels)->pluck('models')->flatten()->isNotEmpty();
@endphp

<div class="model-selector-container" x-data="{ open: false }">
    @if($hasModels)
        <div class="model-selector" @click.away="open = false">
            <button type="button" @click="open = !open" class="model-selector-trigger">
                <i class="iconoir-sparks text-sm"></i>
                <span class="model-selector-value">
                    {{ $selectedModel ?: __('chat.model_selector.select_model') }}
                </span>
                <i class="iconoir-nav-arrow-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1" class="model-selector-dropdown purrai-dropdown" x-cloak>
                @foreach($availableModels as $providerKey => $providerData)
                    <div class="model-selector-group">
                        <div class="model-selector-group-label">{{ $providerData['provider'] }}</div>
                        @foreach($providerData['models'] as $model)
                            <button type="button" wire:click="$set('selectedModel', '{{ $model }}')" @click="open = false"
                                class="model-selector-option {{ $selectedModel === $model ? 'active' : '' }}">
                                <span>{{ str_replace(['-', '_'], ' ', $model) }}</span>
                                @if($selectedModel === $model)
                                    <i class="iconoir-check text-xs"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endforeach
                <div class="text-xs text-center bg-transparent">
                    <a href="{{ route('settings') }}?tab=ai_providers" wire:navigate
                        class="flex justify-between text-gray-500 hover:opacity-75 py-2 px-3">
                        <span>{{ __('chat.model_selector.configure_providers') }}</span>
                        <i class="iconoir-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('settings') }}?tab=ai_providers" wire:navigate class="model-selector-empty">
            <i class="iconoir-warning-triangle text-sm"></i>
            <span>{{ __('chat.model_selector.configure_providers') }}</span>
            <i class="iconoir-arrow-right text-xs"></i>
        </a>
    @endif
</div>