<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Setting;
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

    public function mount(): void
    {
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
            fn($model) => trim($model),
            explode(',', $models)
        )));
    }

    public function render(): mixed
    {
        return view('livewire.settings');
    }
}
