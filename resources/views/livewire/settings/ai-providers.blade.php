<p class="settings-description">
    {{ __('settings.ai_providers.description') }}
</p>

@foreach (config('purrai.ai_providers', []) as $provider)
    <div class="card">
        @foreach ($provider['fields'] as $index => $field)
            @if ($field['name'] === 'key' || $field['name'] === 'url')
                <div class="flex items-end gap-2 @if ($index > 0) mt-4 @endif">
                    <div class="flex-1">
                        <label class="settings-label">
                            {{ __($field['label']) }}
                        </label>
                        <x-ui.form.input
                            type="{{ $field['type'] }}"
                            wire:model.blur="providers.{{ $provider['key'] }}.{{ $field['name'] }}"
                            placeholder="{{ __($field['placeholder']) }}"
                            class="settings-input font-mono text-sm"
                        ></x-ui.form.input>
                    </div>
                    <div class="flex h-full items-center justify-center">
                        <button
                            type="button"
                            wire:click="fetchModels('{{ $provider['key'] }}')"
                            wire:loading.attr="disabled"
                            wire:target="fetchModels('{{ $provider['key'] }}')"
                            class="button"
                            title="{{ __('settings.ai_providers.fetch_models') }}"
                        >
                            <span
                                wire:loading.remove
                                wire:target="fetchModels('{{ $provider['key'] }}')"
                                class="flex h-full items-center justify-center"
                            >
                                <i class="iconoir-refresh text-base"></i>
                            </span>
                            <span
                                wire:loading
                                wire:target="fetchModels('{{ $provider['key'] }}')"
                                class="flex h-full items-center justify-center"
                            >
                                <i class="iconoir-refresh text-base animate-spin"></i>
                            </span>
                        </button>
                    </div>
                </div>
            @else
                <label class="settings-label @if ($index > 0) mt-4 @endif">
                    {{ __($field['label']) }}
                </label>
                <x-ui.form.input
                    type="{{ $field['type'] }}"
                    wire:model.blur="providers.{{ $provider['key'] }}.{{ $field['name'] }}"
                    placeholder="{{ __($field['placeholder']) }}"
                    class="settings-input font-mono text-sm"
                ></x-ui.form.input>

                @if (isset($field['helper']))
                    <p class="help-text">{{ __($field['helper']) }}</p>
                @endif
            @endif
        @endforeach
    </div>
@endforeach
