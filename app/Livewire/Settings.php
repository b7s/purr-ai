<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Setting;
use App\Services\WhisperService;
use Livewire\Component;
use Native\Desktop\Facades\App;

class Settings extends Component
{
    private static string $fakeKey = 'show-fake-key-purrai';

    public string $mascotName = '';

    public string $userName = '';

    public string $userDescription = '';

    public string $responseDetail = 'detailed';

    public string $responseTone = 'basic';

    public bool $respondAsACat = false;

    public array $providers = [];

    public int $deleteOldMessagesDays = 0;

    public int $windowOpacity = 90;

    public int $windowBlur = 8;

    public bool $disableTransparencyMaximized = true;

    public string $themeMode = 'automatic';

    public bool $openAtLogin = false;

    public array $whisperStatus = [];

    public bool $isDownloadingWhisper = false;

    public string $downloadProgress = '';

    public ?string $downloadError = null;

    public bool $speechToTextEnabled = false;

    public bool $useLocalSpeech = true;

    public string $speechProvider = '';

    public string $noiseSuppressionLevel = 'medium';

    public function mount(): void
    {
        $this->checkWhisperStatus();
        $this->speechToTextEnabled = (bool) Setting::get('speech_to_text_enabled', false);
        $this->useLocalSpeech = (bool) Setting::get('use_local_speech', true);
        $this->speechProvider = Setting::get('speech_provider', '');
        $this->noiseSuppressionLevel = Setting::get('noise_suppression_level', 'medium');
        $this->mascotName = Setting::get('mascot_name', config('app.name'));
        $this->userName = Setting::get('user_name', '');
        $this->userDescription = Setting::get('user_description', '');
        $this->responseDetail = Setting::get('response_detail', 'detailed');
        $this->responseTone = Setting::get('response_tone', 'basic');
        $this->respondAsACat = (bool) Setting::get('respond_as_cat', false);

        $this->loadProviders();

        $this->deleteOldMessagesDays = (int) Setting::get('delete_old_messages_days', 0);
        $this->windowOpacity = (int) Setting::get('window_opacity', config('purrai.window.opacity'));
        $this->windowBlur = (int) Setting::get('window_blur', config('purrai.window.blur', 48));
        $this->disableTransparencyMaximized = (bool) Setting::get('disable_transparency_maximized', true);
        $this->themeMode = Setting::get('theme_mode', 'automatic');
        $this->openAtLogin = (bool) Setting::get('open_at_login', false);
    }

    public function save(): void
    {
        Setting::set('mascot_name', $this->mascotName);
        Setting::set('user_name', $this->userName);
        Setting::set('user_description', $this->userDescription);
        Setting::set('response_detail', $this->responseDetail);
        Setting::set('response_tone', $this->responseTone);
        Setting::set('respond_as_cat', $this->respondAsACat);

        $this->saveProviders();

        Setting::set('delete_old_messages_days', $this->deleteOldMessagesDays);
        Setting::set('window_opacity', $this->windowOpacity);
        Setting::set('window_blur', $this->windowBlur);
        Setting::set('disable_transparency_maximized', $this->disableTransparencyMaximized);
        Setting::set('theme_mode', $this->themeMode);
        Setting::set('open_at_login', $this->openAtLogin);
        Setting::set('speech_to_text_enabled', $this->speechToTextEnabled);
        Setting::set('use_local_speech', $this->useLocalSpeech);
        Setting::set('speech_provider', $this->speechProvider);
        Setting::set('noise_suppression_level', $this->noiseSuppressionLevel);

        $this->dispatch('settings-saved');
        $this->dispatch('opacity-changed', opacity: $this->windowOpacity);
        $this->dispatch('blur-changed', blur: $this->windowBlur);
        $this->dispatch('theme-changed', theme: $this->themeMode);
    }

    public function updatedWindowOpacity(): void
    {
        $this->save();
    }

    public function updatedWindowBlur(): void
    {
        $this->save();
    }

    public function updatedResponseDetail(): void
    {
        $this->save();
    }

    public function updatedResponseTone(): void
    {
        $this->save();
    }

    public function updatedRespondAsACat(): void
    {
        $this->save();
    }

    public function updatedDeleteOldMessagesDays(): void
    {
        if ($this->deleteOldMessagesDays < 0) {
            $this->deleteOldMessagesDays = 0;
        }
        $this->save();
    }

    public function updatedDisableTransparencyMaximized(): void
    {
        $this->save();
        $this->dispatch('transparency-setting-changed', enabled: $this->disableTransparencyMaximized);
    }

    public function updatedThemeMode(): void
    {
        $this->save();
    }

    public function updatedProviders(): void
    {
        $this->save();
    }

    public function updatedMascotName(): void
    {
        $this->save();
    }

    public function updatedUserName(): void
    {
        $this->save();
    }

    public function updatedSpeechToTextEnabled(): void
    {
        $this->save();
    }

    public function updatedUseLocalSpeech(): void
    {
        $this->save();
    }

    public function updatedSpeechProvider(): void
    {
        $this->save();
    }

    public function updatedNoiseSuppressionLevel(): void
    {
        $this->save();
    }

    public function updatedUserDescription(): void
    {
        $this->save();
    }

    public function updatedOpenAtLogin(): void
    {
        if (! is_linux()) {
            $this->save();

            if (class_exists(App::class)) {
                App::openAtLogin($this->openAtLogin);
            }
        }
    }

    private function loadProviders(): void
    {
        $providersConfig = config('purrai.ai_providers', []);

        foreach ($providersConfig as $providerConfig) {
            $configKey = $providerConfig['config_key'];
            $encrypted = $providerConfig['encrypted'];

            $data = $encrypted
                ? Setting::getJsonDecrypted($configKey, [])
                : Setting::getJson($configKey, []);

            $this->providers[$providerConfig['key']] = [];

            foreach ($providerConfig['fields'] as $field) {
                $fieldName = $field['name'];
                $value = $data[$fieldName] ?? '';

                if ($fieldName === 'models' && is_array($value)) {
                    $value = implode(', ', $value);
                } elseif ($fieldName === 'key' && ! empty($value)) {
                    $value = self::$fakeKey;
                }

                $this->providers[$providerConfig['key']][$fieldName] = $value;
            }
        }
    }

    private function saveProviders(): void
    {
        $providersConfig = config('purrai.ai_providers', []);

        foreach ($providersConfig as $providerConfig) {
            $configKey = $providerConfig['config_key'];
            $encrypted = $providerConfig['encrypted'];
            $providerKey = $providerConfig['key'];

            $data = [];

            foreach ($providerConfig['fields'] as $field) {
                $fieldName = $field['name'];
                $value = $this->providers[$providerKey][$fieldName] ?? '';

                if ($fieldName === 'key') {
                    if ($value === self::$fakeKey) {
                        $existingConfig = $encrypted
                            ? Setting::getJsonDecrypted($configKey, [])
                            : Setting::getJson($configKey, []);
                        $value = $existingConfig['key'] ?? '';
                    }
                    $data[$fieldName] = $value;
                } elseif ($fieldName === 'models') {
                    $data[$fieldName] = $this->parseModels($value);
                } else {
                    $data[$fieldName] = $value;
                }
            }

            if ($encrypted) {
                Setting::setJsonEncrypted($configKey, $data);
            } else {
                Setting::setJson($configKey, $data);
            }

            Setting::query()->where('key', $configKey)->update(['is_ai_provider' => true]);
        }
    }

    /**
     * Parse comma-separated models string into array
     */
    private function parseModels(string $models): array
    {
        if (empty(trim($models))) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($model) => trim($model),
            explode(',', $models)
        )));
    }

    public function checkWhisperStatus(): void
    {
        try {
            $whisperService = app(WhisperService::class);
            $this->whisperStatus = $whisperService->getStatus();
        } catch (\Throwable $e) {
            $this->whisperStatus = [
                'binary' => false,
                'model' => false,
                'ffmpeg' => false,
                'gpu' => false,
            ];
        }
    }

    public function downloadWhisper(): void
    {
        $this->isDownloadingWhisper = true;
        $this->downloadError = null;
        $this->downloadProgress = __('settings.other.download_starting');

        try {
            $whisperService = app(WhisperService::class);
            $status = $whisperService->getStatus();

            // Download FFmpeg
            if (! $status['ffmpeg']) {
                $this->downloadProgress = __('settings.other.downloading_ffmpeg');
                $this->dispatch('progress-updated');

                if (! $whisperService->downloadFfmpeg()) {
                    throw new \Exception(__('settings.other.ffmpeg_download_failed'));
                }
            }

            // Download Whisper binary
            if (! $status['binary']) {
                $this->downloadProgress = __('settings.other.downloading_whisper_binary');
                $this->dispatch('progress-updated');

                if (! $whisperService->downloadBinary()) {
                    throw new \Exception(__('settings.other.whisper_binary_download_failed'));
                }
            }

            // Download model
            if (! $status['model']) {
                $this->downloadProgress = __('settings.other.downloading_whisper_model');
                $this->dispatch('progress-updated');

                if (! $whisperService->downloadModel()) {
                    throw new \Exception(__('settings.other.whisper_model_download_failed'));
                }
            }

            $this->downloadProgress = __('settings.other.download_complete');
            $this->checkWhisperStatus();
            $this->dispatch('whisper-setup-complete');
        } catch (\Exception $e) {
            $this->downloadError = $e->getMessage();
            $this->downloadProgress = __('settings.other.download_failed');
            $this->dispatch('whisper-setup-failed', message: $e->getMessage());
        } finally {
            $this->isDownloadingWhisper = false;
        }
    }

    public function getSpeechProviderOptions(): array
    {
        return Setting::getSpeechProviderOptions();
    }

    public function render(): mixed
    {
        return view('livewire.settings');
    }
}
