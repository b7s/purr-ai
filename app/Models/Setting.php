<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'is_ai_provider',
    ];

    /**
     * @return array<string, array{provider: string, models: array<string>}>
     */
    public static function getAvailableModels(): array
    {
        $providers = static::where('is_ai_provider', true)->get();
        $result = [];

        foreach ($providers as $provider) {
            $config = static::getProviderConfig($provider->key);

            if (empty($config['models'])) {
                continue;
            }

            $hasKey = ! empty($config['key']) || ! empty($config['url']);

            if (! $hasKey) {
                continue;
            }

            $providerName = static::getProviderDisplayName($provider->key);
            $result[$provider->key] = [
                'provider' => $providerName,
                'models' => $config['models'],
            ];
        }

        return $result;
    }

    public static function getSelectedModel(): ?string
    {
        return static::get('selected_model');
    }

    public static function setSelectedModel(string $model): void
    {
        static::set('selected_model', $model);
    }

    /**
     * @return array{key?: string, url?: string, models: array<string>}
     */
    private static function getProviderConfig(string $key): array
    {
        if ($key === 'ollama_config') {
            return static::getJson($key, ['url' => '', 'models' => []]);
        }

        return static::getJsonDecrypted($key, ['key' => '', 'models' => []]);
    }

    private static function getProviderDisplayName(string $key): string
    {
        return match ($key) {
            'openai_config' => 'OpenAI',
            'anthropic_config' => 'Anthropic',
            'google_config' => 'Google',
            'ollama_config' => 'Ollama',
            default => ucfirst(str_replace('_config', '', $key)),
        };
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = "settings.{$key}";
        $cached = cache()->get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $setting = static::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;

        cache()->put($cacheKey, $value, 3600);

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        cache()->put("settings.{$key}", $value, 3600);
    }

    public static function getEncrypted(string $key, mixed $default = null): mixed
    {
        $value = static::get($key, $default);

        if ($value === null || $value === $default) {
            return $default;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return $default;
        }
    }

    public static function setEncrypted(string $key, mixed $value): void
    {
        static::set($key, Crypt::encryptString($value));
    }

    public static function getJson(string $key, mixed $default = null): mixed
    {
        $value = static::get($key, null);

        if ($value === null) {
            return $default;
        }

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            return $decoded;
        } catch (\Exception) {
            return $default;
        }
    }

    public static function setJson(string $key, mixed $value): void
    {
        try {
            $encoded = json_encode($value, JSON_THROW_ON_ERROR);
            static::set($key, $encoded);
        } catch (\Exception) {
        }
    }

    public static function getJsonDecrypted(string $key, mixed $default = null): mixed
    {
        $value = static::get($key, null);

        if ($value === null) {
            return $default;
        }

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            if (isset($decoded['key']) && ! empty($decoded['key'])) {
                try {
                    $decoded['key'] = Crypt::decryptString($decoded['key']);
                } catch (\Exception) {
                    $decoded['key'] = '';
                }
            }

            return $decoded;
        } catch (\Exception) {
            return $default;
        }
    }

    public static function setJsonEncrypted(string $key, mixed $value): void
    {
        try {
            if (isset($value['key']) && ! empty($value['key'])) {
                $value['key'] = Crypt::encryptString($value['key']);
            }

            $encoded = json_encode($value, JSON_THROW_ON_ERROR);
            static::set($key, $encoded);
        } catch (\Exception) {
        }
    }

    /**
     * Validate and return the selected speech provider model
     *
     * @return string|false Returns model name if valid, false otherwise
     */
    public static function getValidatedSpeechModel(): string|false
    {
        $speechProviderValue = static::get('speech_provider');
        if (empty($speechProviderValue)) {
            return false;
        }

        $parts = explode(':', $speechProviderValue);
        if (\count($parts) < 2 || empty($parts[0]) || empty($parts[1])) {
            return false;
        }

        [$provider, $model] = $parts;

        $providers = config('purrai.ai_providers', []);
        $providerConfig = collect($providers)->firstWhere('key', $provider);

        if (! $providerConfig) {
            return false;
        }

        $availableModels = $providerConfig['models']['speech_to_text'] ?? [];
        if (! \in_array($model, $availableModels, true)) {
            return false;
        }

        $configKey = $providerConfig['config_key'];
        $encrypted = $providerConfig['encrypted'];

        if ($encrypted) {
            $config = static::getJsonDecrypted($configKey);
            $hasConfig = ! empty($config['key']);
        } else {
            $config = static::getJson($configKey);
            $hasConfig = ! empty($config['url']);
        }

        if (! $hasConfig) {
            return false;
        }

        return $model;
    }

    /**
     * Get the API key for a specific provider
     *
     * @return string|null Returns the API key or null if not found
     */
    public static function getProviderApiKey(string $provider): ?string
    {
        $providers = config('purrai.ai_providers', []);
        $providerConfig = collect($providers)->firstWhere('key', $provider);

        if (! $providerConfig) {
            return null;
        }

        $configKey = $providerConfig['config_key'];
        $encrypted = $providerConfig['encrypted'];

        if ($encrypted) {
            $config = static::getJsonDecrypted($configKey);

            return $config['key'] ?? null;
        }

        $config = static::getJson($configKey);

        return $config['url'] ?? null;
    }

    /**
     * Get speech provider options grouped by provider
     * Returns format compatible with select.blade.php component
     *
     * @return array<string, array<string, string>>
     */
    public static function getSpeechProviderOptions(): array
    {
        $providers = config('purrai.ai_providers', []);
        $result = [];

        foreach ($providers as $provider) {
            $speechModels = $provider['models']['speech_to_text'] ?? [];

            // Skip providers without speech-to-text models
            if (empty($speechModels)) {
                continue;
            }

            $configKey = $provider['config_key'];
            $encrypted = $provider['encrypted'];

            if ($encrypted) {
                $config = static::getJsonDecrypted($configKey);
                $hasConfig = ! empty($config['key']);
            } else {
                $config = static::getJson($configKey);
                $hasConfig = ! empty($config['url']);
            }

            if (! $hasConfig) {
                continue;
            }

            $providerKey = $provider['key'];
            $providerName = static::getProviderDisplayName($providerKey.'_config');

            $result[$providerName] = [];
            foreach ($speechModels as $model) {
                $result[$providerName]["{$providerKey}:{$model}"] = $model;
            }
        }

        return $result;
    }
}
