<x-slot name="headerActions">
    <a
        href="{{ getPreviousChatUrl() }}"
        wire:navigate
        class="circle-btn ghost"
    >
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

<div
    class="h-full flex flex-col overflow-y-auto"
    @keydown.escape.window="window.location.href = '{{ getPreviousChatUrl() }}'"
    tabindex="-1"
>
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
        <x-ui.form.tabs
            :tabs="$tabs"
            :active="request()->input('tab')"
        >
            {{-- Chat Settings Tab --}}
            <x-ui.form.tab-content name="chat">
                <div class="card">
                    <x-ui.form.input
                        :label="__('settings.chat.mascot_name')"
                        model="mascotName"
                        :placeholder="__('settings.chat.mascot_name_placeholder')"
                    />
                </div>

                <div class="card space-y-6">
                    <x-ui.form.radio-group
                        :label="__('settings.chat.response_detail')"
                        :options="$responseDetailOptions"
                        model="responseDetail"
                    />

                    <x-ui.form.radio-group
                        :label="__('settings.chat.response_tone')"
                        :options="$responseToneOptions"
                        model="responseTone"
                        columns="2 sm:grid-cols-3 md:grid-cols-4"
                    />
                </div>

                <div class="card">
                    <x-ui.form.toggle
                        :label="new \Illuminate\Support\HtmlString('<img src=\'' . asset('images/mascot/logo.svg') . '\' alt=\'\' class=\'w-8 inline-block me-2\'>' . __('settings.chat.respond_as_cat'))"
                        :model="'respondAsACat'"
                        :checked="$respondAsACat"
                    >
                    </x-ui.form.toggle>
                </div>

                {{-- Speech Recognition Settings --}}
                <div
                    class="card space-y-4"
                    id="active-speech-recognition-setting"
                >
                    <label class="settings-label">
                        {{ __('settings.other.speech_recognition') }}
                    </label>

                    @if (hasWhisperPendingAlert())
                        <div class="whisper-alert">
                            <div class="whisper-alert-content">
                                <i class="iconoir-warning-triangle whisper-alert-icon"></i>
                                <div class="whisper-alert-body">
                                    <h3 class="whisper-alert-title">
                                        {{ __('settings.other.speech_recognition_setup') }}
                                    </h3>

                                    {{-- Speech Recognition Setup Alert --}}
                                    @if ($useLocalSpeech)
                                        <x-settings.whisper-status
                                            :status="$whisperStatus"
                                            :isDownloading="$isDownloadingWhisper"
                                            :progress="$downloadProgress"
                                            :error="$downloadError"
                                        />
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <x-ui.form.toggle
                        :label="__('settings.speech.enable')"
                        :description="__('settings.speech.enable_description')"
                        model="speechToTextEnabled"
                        :checked="$speechToTextEnabled"
                    />

                    @if ($speechToTextEnabled)
                        <x-ui.form.toggle
                            :label="__('settings.speech.use_local')"
                            :description="new \Illuminate\Support\HtmlString(view('components.ui.form.badge', ['slot' => __('settings.speech.private')])->render() . '&nbsp;&nbsp;' . __('settings.speech.use_local_description'))"
                            model="useLocalSpeech"
                            :checked="$useLocalSpeech"
                        />

                        @if (!$useLocalSpeech)
                            <x-ui.form.select
                                :label="__('settings.speech.provider') . ' *'"
                                :description="__('settings.speech.provider_description')"
                                :placeholder="__('settings.speech.provider_placeholder')"
                                model="speechProvider"
                                :options="$this->getSpeechProviderOptions()"
                            />
                        @endif

                        <x-ui.form.toggle
                            :label="__('settings.speech.auto_send')"
                            :description="__('settings.speech.auto_send_description')"
                            model="autoSendAfterTranscription"
                            :checked="$autoSendAfterTranscription ?? false"
                        />

                        {{-- AudioDevice Selection --}}
                        <div
                            class="audio_device-settings"
                            x-data="audio_deviceSelector"
                        >
                            <label class="settings-label">
                                {{ __('settings.speech.audio_device_settings') }}
                            </label>

                            <div class="space-y-4">
                                {{-- AudioDevice Select --}}
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <label class="settings-label text-sm">
                                            {{ __('settings.speech.select_audio_device') }}
                                        </label>
                                        <select
                                            x-model="selectedDeviceId"
                                            @change="selectDevice($event.target.value)"
                                            class="settings-input"
                                            :disabled="loading"
                                        >
                                            <template x-if="loading">
                                                <option>{{ __('settings.speech.loading_devices') }}</option>
                                            </template>
                                            <template x-if="!loading && devices.length === 0">
                                                <option value="default">{{ __('settings.speech.default_audio_device') }}
                                                </option>
                                            </template>
                                            <template
                                                x-for="device in devices"
                                                :key="device.id"
                                            >
                                                <option
                                                    :value="device.id"
                                                    x-text="device.label"
                                                ></option>
                                            </template>
                                        </select>
                                        <p class="help-text">
                                            {{ __('settings.speech.select_audio_device_description') }}
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        @click="refreshDevices()"
                                        class="w-10 h-10 mb-1 button"
                                        :disabled="loading"
                                        title="{{ __('settings.speech.refresh_devices') }}"
                                    >
                                        <i
                                            class="iconoir-refresh text-lg"
                                            :class="{ 'animate-spin': loading }"
                                        ></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Noise Suppression Level --}}
                        <x-ui.form.radio-group
                            :label="__('settings.speech.noise_suppression')"
                            :description="__('settings.speech.noise_suppression_description')"
                            model="noiseSuppressionLevel"
                            columns="2 sm:grid-cols-3 md:grid-cols-4"
                            :options="[
                                'disabled' => [
                                    'label' => __('settings.speech.noise_level_disabled'),
                                    'description' => __('settings.speech.noise_level_disabled_desc'),
                                    'icon' => 'sound-off',
                                ],
                                'light' => [
                                    'label' => __('settings.speech.noise_level_light'),
                                    'description' => __('settings.speech.noise_level_light_desc'),
                                    'icon' => 'sound-low',
                                ],
                                'medium' => [
                                    'label' => __('settings.speech.noise_level_medium'),
                                    'description' => __('settings.speech.noise_level_medium_desc'),
                                    'icon' => 'sound-min',
                                ],
                                'high' => [
                                    'label' => __('settings.speech.noise_level_high'),
                                    'description' => __('settings.speech.noise_level_high_desc'),
                                    'icon' => 'sound-high',
                                ],
                            ]"
                        />
                    @endif
                </div>

                <div class="card">
                    <label class="settings-label">
                        {{ __('settings.chat.user_description') }}
                    </label>
                    <x-ui.form.input
                        type="text"
                        wire:model.blur="userName"
                        placeholder="{{ __('settings.chat.user_name_placeholder') }}"
                        class="settings-input"
                    ></x-ui.form.input>

                    <x-ui.form.textarea
                        wire:model.blur="userDescription"
                        placeholder="{{ __('settings.chat.user_description_placeholder') }}"
                        rows="3"
                        class="settings-input resize-none mt-4"
                    ></x-ui.form.textarea>
                </div>
            </x-ui.form.tab-content>

            {{-- AI Providers Tab --}}
            <x-ui.form.tab-content name="ai_providers">
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
                                    <button
                                        type="button"
                                        wire:click="fetchModels('{{ $provider['key'] }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="fetchModels('{{ $provider['key'] }}')"
                                        class="settings-button"
                                        title="{{ __('settings.ai_providers.fetch_models') }}"
                                    >
                                        <span
                                            wire:loading.remove
                                            wire:target="fetchModels('{{ $provider['key'] }}')"
                                        >
                                            <i class="iconoir-refresh text-base"></i>
                                        </span>
                                        <span
                                            wire:loading
                                            wire:target="fetchModels('{{ $provider['key'] }}')"
                                        >
                                            <i class="iconoir-refresh text-base animate-spin"></i>
                                        </span>
                                    </button>
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
            </x-ui.form.tab-content>

            {{-- Other Settings Tab --}}
            <x-ui.form.tab-content name="other">
                <x-ui.form.radio-group
                    :label="__('settings.other.theme_mode')"
                    :description="__('settings.other.theme_mode_description')"
                    :options="$themeModeOptions"
                    model="themeMode"
                />

                <div class="card">
                    <x-ui.form.input
                        type="number"
                        :label="__('settings.other.delete_old_messages')"
                        :description="__('settings.other.delete_old_messages_description')"
                        :helpText="__('settings.other.delete_old_messages_helper')"
                        model="deleteOldMessagesDays"
                        class="w-full sm:w-40"
                        min="0"
                        step="1"
                        placeholder="0"
                    />
                </div>

                <div
                    class="card"
                    x-data="timezoneSelector"
                >
                    <x-ui.form.input
                        :label="__('settings.other.timezone')"
                        :description="__('settings.other.timezone_description')"
                        :helpText="__('settings.other.timezone_helper')"
                        model="timezone"
                        :placeholder="__('settings.other.timezone_placeholder')"
                        x-ref="timezoneInput"
                    />
                </div>

                @if (!is_linux())
                    <x-ui.form.toggle
                        :label="__('settings.other.open_at_login')"
                        :description="__('settings.other.open_at_login_description')"
                        model="openAtLogin"
                        :checked="$openAtLogin"
                    />
                @endif

                <div class="card space-y-4">
                    <x-ui.form.slider
                        :label="__('settings.other.window_opacity')"
                        :description="__('settings.other.window_opacity_description')"
                        model="windowOpacity"
                        min="50"
                        max="100"
                        :value="$windowOpacity"
                        suffix="%"
                    />

                    <x-ui.form.slider
                        :label="__('settings.other.window_blur')"
                        :description="__('settings.other.window_blur_description')"
                        :helpText="__('settings.other.window_blur_helper')"
                        model="windowBlur"
                        min="0"
                        max="100"
                        :value="$windowBlur"
                        suffix="px"
                    >
                    </x-ui.form.slider>

                    <x-ui.form.toggle
                        :label="__('settings.other.disable_transparency_maximized')"
                        :description="__('settings.other.disable_transparency_maximized_description')"
                        model="disableTransparencyMaximized"
                        :checked="$disableTransparencyMaximized"
                        class="mt-4"
                    />
                </div>

                {{-- Danger Zone --}}
                <div class="card border-2 border-red-500/20! dark:border-red-500/30! space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 rounded-lg bg-red-500/10 dark:bg-red-500/20 flex items-center justify-center">
                            <i class="iconoir-warning-triangle text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-semibold text-red-600 dark:text-red-400 mb-1">
                                {{ __('settings.danger_zone.title') }}
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                {{ __('settings.danger_zone.description') }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-red-500/20 dark:border-red-500/30">
                        <x-ui.form.toggle
                            :label="__('settings.danger_zone.allow_destructive_operations')"
                            :description="__('settings.danger_zone.allow_destructive_operations_description')"
                            model="allowDestructiveFileOperations"
                            :checked="$allowDestructiveFileOperations"
                        />
                    </div>
                </div>
            </x-ui.form.tab-content>
        </x-ui.form.tabs>
    </div>

    {{-- Saving Indicator --}}
    <div
        wire:loading
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 text-xs bg-slate-500/50 p-1 px-2 text-slate-50 text-center rounded-xl"
    >
        <x-ui.loading-icon />
        <span>{{ __('settings.saving') }}</span>
    </div>
</div>
