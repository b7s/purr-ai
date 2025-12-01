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

@php($respondAsACatLabel = new \Illuminate\Support\HtmlString(sprintf('<img src="%s" alt="" class="w-8 inline-block me-2">%s', asset('images/mascot/logo.svg'), __('settings.chat.respond_as_cat'))))

<div class="card">
    <x-ui.form.toggle
        :label="$respondAsACatLabel"
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

                    <div class="flex items-center justify-center">
                        <button
                            type="button"
                            @click="refreshDevices()"
                            class="button"
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
