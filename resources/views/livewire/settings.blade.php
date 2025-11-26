<x-slot name="headerActions">
    <a href="{{ getPreviousChatUrl() }}" wire:navigate class="circle-btn ghost">
        <i class="iconoir-arrow-left text-xl"></i>
    </a>
</x-slot>

@php
    $tabs = [
        'chat' => ['label' => __('settings.tabs.chat')],
        'ai_providers' => ['label' => __('settings.tabs.ai_providers'), 'icon' => 'sparks'],
        'other' => ['label' => __('settings.tabs.other')],
    ];

    $responseDetailOptions = [
        'detailed' => ['label' => __('settings.chat.response_detail_detailed')],
        'short' => ['label' => __('settings.chat.response_detail_short')],
    ];

    $responseToneOptions = collect(config('purrai.response_tones'))
        ->mapWithKeys(function ($tone) {
            return [
                $tone['value'] => [
                    'icon' => $tone['icon'],
                    'label' => __($tone['label']),
                    'description' => __($tone['description']),
                    'height' => 'h-24',
                    'class' => 'flex flex-col items-center justify-center gap-1.5 h-full px-2',
                    'labelClass' => 'text-sm font-medium',
                ],
            ];
        })
        ->toArray();

    $themeModeOptions = [
        'light' => [
            'icon' => 'sun-light',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_light'),
            'class' => 'flex items-center justify-center',
        ],
        'dark' => [
            'icon' => 'half-moon',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_dark'),
            'class' => 'flex items-center justify-center',
        ],
        'automatic' => [
            'icon' => 'settings',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_automatic'),
            'class' => 'flex items-center justify-center',
        ],
    ];
@endphp

<div class="h-full flex flex-col overflow-y-auto"
    @keydown.escape.window="window.location.href = '{{ getPreviousChatUrl() }}'" tabindex="-1">
    <div class="w-full max-w-4xl mx-auto px-6 md:px-10 py-6 md:py-10 pb-24 space-y-8">
        {{-- Header --}}
        <div class="space-y-2">
            <h1 class="settings-title">
                {{ __('settings.title') }}
            </h1>
            <p class="settings-description">
                {{ __('settings.auto_save_notice') }}
            </p>
        </div>

        {{-- Tabs --}}
        <x-ui.tabs :tabs="$tabs" :active="request()->input('tab')">
            {{-- Chat Settings Tab --}}
            <x-ui.tab-content name="chat">
                <div class="card">
                    <x-ui.input :label="__('settings.chat.mascot_name')" model="mascotName"
                        :placeholder="__('settings.chat.mascot_name_placeholder')" />
                </div>

                <x-ui.radio-group :label="__('settings.chat.response_detail')" :options="$responseDetailOptions"
                    model="responseDetail" />

                <x-ui.radio-group :label="__('settings.chat.response_tone')" :options="$responseToneOptions"
                    model="responseTone" columns="2 sm:grid-cols-3 md:grid-cols-4" />

                <div class="card">
                    <x-ui.toggle :label="__('settings.chat.respond_as_cat')" :model="'respondAsACat'"
                        :checked="$respondAsACat"></x-ui.toggle>
                </div>

                <div class="card">
                    <label class="settings-label">
                        {{ __('settings.chat.user_description') }}
                    </label>
                    <x-ui.input type="text" wire:model.blur="userName"
                        placeholder="{{ __('settings.chat.user_name_placeholder') }}"
                        class="settings-input"></x-ui.input>

                    <x-ui.textarea wire:model.blur="userDescription"
                        placeholder="{{ __('settings.chat.user_description_placeholder') }}" rows="3"
                        class="settings-input resize-none mt-4"></x-ui.textarea>
                </div>
            </x-ui.tab-content>

            {{-- AI Providers Tab --}}
            <x-ui.tab-content name="ai_providers">
                <p class="settings-description">
                    {{ __('settings.ai_providers.description') }}
                </p>

                @foreach (config('purrai.ai_providers', []) as $provider)
                    <div class="card">
                        @foreach ($provider['fields'] as $index => $field)
                            <label class="settings-label @if ($index > 0) mt-4 @endif">
                                {{ __($field['label']) }}
                            </label>
                            <x-ui.input type="{{ $field['type'] }}"
                                wire:model.blur="providers.{{ $provider['key'] }}.{{ $field['name'] }}"
                                placeholder="{{ __($field['placeholder']) }}"
                                class="settings-input font-mono text-sm"></x-ui.input>

                            @if (isset($field['helper']))
                                <p class="help-text">{{ __($field['helper']) }}</p>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </x-ui.tab-content>

            {{-- Other Settings Tab --}}
            <x-ui.tab-content name="other">
                <x-ui.radio-group :label="__('settings.other.theme_mode')"
                    :description="__('settings.other.theme_mode_description')" :options="$themeModeOptions"
                    model="themeMode" />

                <div class="card">
                    <x-ui.input type="number" :label="__('settings.other.delete_old_messages')"
                        :description="__('settings.other.delete_old_messages_description')"
                        :helpText="__('settings.other.delete_old_messages_helper')" model="deleteOldMessagesDays"
                        class="w-full sm:w-40" min="0" step="1" placeholder="0" />
                </div>

                @if (!is_linux())
                    <x-ui.toggle :label="__('settings.other.open_at_login')"
                        :description="__('settings.other.open_at_login_description')" model="openAtLogin"
                        :checked="$openAtLogin" />
                @endif

                <x-ui.slider :label="__('settings.other.window_opacity')"
                    :description="__('settings.other.window_opacity_description')" model="windowOpacity" min="50"
                    max="100" :value="$windowOpacity" suffix="%" />

                <x-ui.slider :label="__('settings.other.window_blur')"
                    :description="__('settings.other.window_blur_description')"
                    :helpText="__('settings.other.window_blur_helper')" model="windowBlur" min="0" max="100"
                    :value="$windowBlur" suffix="px">
                    <x-ui.toggle :label="__('settings.other.disable_transparency_maximized')"
                        :description="__('settings.other.disable_transparency_maximized_description')"
                        model="disableTransparencyMaximized" :checked="$disableTransparencyMaximized" class="mt-4" />
                </x-ui.slider>
            </x-ui.tab-content>
        </x-ui.tabs>
    </div>

    {{-- Saving Indicator --}}
    <div wire:loading
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 text-xs bg-slate-500/50 p-1 px-2 text-slate-50 text-center rounded-xl">
        <x-ui.loading-icon></x-ui.loading-icon>
        <span>{{ __('settings.saving') }}</span>
    </div>
</div>