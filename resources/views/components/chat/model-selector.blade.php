@props(['availableModels' => [], 'selectedModel' => ''])

@php
    $hasModels = !empty($availableModels) && collect($availableModels)->pluck('models')->flatten()->isNotEmpty();
@endphp

<div
    class="model-selector-container"
    x-data="{
        open: false,
        filterOpen: true,
        filterText: '',
        toggleFilter() {
            this.filterOpen = !this.filterOpen;
            if (this.filterOpen) {
                this.$nextTick(() => this.$refs.filterInput.focus());
            } else {
                this.filterText = '';
            }
        },
        closeFilter() {
            this.filterOpen = false;
            this.filterText = '';
        },
        matchesFilter(text) {
            if (!this.filterText) return true;
            return text.toLowerCase().includes(this.filterText.toLowerCase());
        }
    }"
>
    @if ($hasModels)
        <div
            class="model-selector"
            @click.away="open = false"
        >
            <button
                type="button"
                @click="() => {
                    open = !open;
                    if(open)
                        $nextTick(() => $refs.filterInput.focus());
                }"
                class="model-selector-trigger"
            >
                <i class="iconoir-sparks text-sm"></i>
                <span class="model-selector-value">
                    @if ($selectedModel)
                        {{ str_replace(['-', '_'], ' ', explode(':', $selectedModel)[1] ?? $selectedModel) }}
                    @else
                        {{ __('chat.model_selector.select_model') }}
                    @endif
                </span>
                <i
                    class="iconoir-nav-arrow-down text-xs transition-transform"
                    :class="{ 'rotate-180': open }"
                ></i>
            </button>

            <div
                x-show="open"
                x-transition
                class="model-selector-dropdown purrai-opacity-box"
                x-cloak
            >
                <div class="max-h-52 overflow-y-auto">
                    @foreach ($availableModels as $providerKey => $providerData)
                        @php
                            $providerIdentifier = str_replace('_config', '', $providerKey);
                        @endphp
                        <div
                            class="model-selector-group"
                            x-show="[{{ implode(',', array_map(fn($m) => "'$m'", $providerData['models'])) }}].some(model => matchesFilter(model.replace(/[-_]/g, ' ')))"
                        >
                            <div class="model-selector-group-label">
                                <span>{{ $providerData['provider'] }}</span>
                            </div>

                            @foreach ($providerData['models'] as $model)
                                @php
                                    $fullModelName = $providerIdentifier . ':' . $model;
                                @endphp
                                <button
                                    type="button"
                                    wire:click="$set('selectedModel', '{{ $fullModelName }}')"
                                    @click="open = false"
                                    class="model-selector-option {{ $selectedModel === $fullModelName ? 'active' : '' }}"
                                    x-show="matchesFilter('{{ str_replace(['-', '_'], ' ', $model) }}')"
                                >
                                    <span>{{ str_replace(['-', '_'], ' ', $model) }}</span>
                                    @if ($selectedModel === $fullModelName)
                                        <i class="iconoir-check text-xs"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="model-selector-footer">
                    <button
                        type="button"
                        @click="toggleFilter()"
                        class="py-3 pr-2 hover:opacity-75 shrink-0 cursor-pointer"
                        :class="{ 'opacity-100': filterOpen, 'opacity-75': !filterOpen }"
                        title="{{ __('chat.model_selector.filter_models') }}"
                    >
                        <i class="iconoir-search"></i>
                    </button>
                    <div
                        x-show="filterOpen"
                        x-transition
                        class="flex-1"
                        x-cloak
                    >
                        <x-ui.input
                            x-ref="filterInput"
                            x-model="filterText"
                            @keydown.escape="closeFilter()"
                            type="text"
                            placeholder="{{ __('chat.model_selector.filter_placeholder') }}"
                            class="py-1! px-2! text-xs w-full rounded-md"
                        />
                    </div>
                    <a
                        href="{{ route('settings') }}?tab=ai_providers"
                        wire:navigate
                        class="py-3 pl-2 hover:opacity-75 shrink-0"
                        title="{{ __('chat.model_selector.configure_providers') }}"
                    >
                        <i class="iconoir-plus-circle"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <a
            href="{{ route('settings') }}?tab=ai_providers"
            wire:navigate
            class="model-selector-empty"
        >
            <i class="iconoir-warning-triangle text-sm"></i>
            <span>{{ __('chat.model_selector.configure_providers') }}</span>
            <i class="iconoir-arrow-right text-xs"></i>
        </a>
    @endif
</div>
