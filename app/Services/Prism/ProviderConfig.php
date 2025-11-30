<?php

declare(strict_types=1);

namespace App\Services\Prism;

use App\Models\Setting;
use Prism\Prism\Enums\Provider;

class ProviderConfig
{
    /**
     * @var array<string, array{provider: Provider, config_key: string, encrypted: bool}>
     */
    private const PROVIDER_MAP = [
        'openai' => [
            'provider' => Provider::OpenAI,
            'config_key' => 'openai_config',
            'encrypted' => true,
            'api_key_field' => 'key',
        ],
        'anthropic' => [
            'provider' => Provider::Anthropic,
            'config_key' => 'anthropic_config',
            'encrypted' => true,
            'api_key_field' => 'key',
        ],
        'google' => [
            'provider' => Provider::Gemini,
            'config_key' => 'google_config',
            'encrypted' => true,
            'api_key_field' => 'key',
        ],
        'xai' => [
            'provider' => Provider::XAI,
            'config_key' => 'xai_config',
            'encrypted' => true,
            'api_key_field' => 'key',
        ],
        'ollama' => [
            'provider' => Provider::Ollama,
            'config_key' => 'ollama_config',
            'encrypted' => false,
            'api_key_field' => 'url',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const PROVIDER_NAMESPACE_MAP = [
        'openai' => 'OpenAI',
        'anthropic' => 'Anthropic',
        'google' => 'Gemini',
        'xai' => 'XAI',
        'ollama' => 'Ollama',
        'deepseek' => 'DeepSeek',
        'groq' => 'Groq',
        'mistral' => 'Mistral',
        'openrouter' => 'OpenRouter',
    ];

    /**
     * Parse selected model string (format: "provider:model")
     *
     * @return array{provider: string, model: string}|null
     */
    public function parseSelectedModel(?string $selectedModel): ?array
    {
        if (empty($selectedModel)) {
            return null;
        }

        $parts = explode(':', $selectedModel, 2);

        if (\count($parts) !== 2) {
            return null;
        }

        return [
            'provider' => $parts[0],
            'model' => $parts[1],
        ];
    }

    public function getPrismProvider(string $providerKey): ?Provider
    {
        return self::PROVIDER_MAP[$providerKey]['provider'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getProviderConfig(string $providerKey): array
    {
        $mapping = self::PROVIDER_MAP[$providerKey] ?? null;

        if (! $mapping) {
            return [];
        }

        $configKey = $mapping['config_key'];
        $encrypted = $mapping['encrypted'];

        if ($encrypted) {
            $config = Setting::getJsonDecrypted($configKey, []);
        } else {
            $config = Setting::getJson($configKey, []);
        }

        $apiKeyField = $mapping['api_key_field'];
        $apiKey = $config[$apiKeyField] ?? '';

        if (empty($apiKey)) {
            return [];
        }

        if ($providerKey === 'ollama') {
            return ['url' => $apiKey];
        }

        return ['api_key' => $apiKey];
    }

    /**
     * Get supported media types by checking which mappers exist for the provider
     *
     * @return array<string>
     */
    public function getSupportedMediaTypes(string $providerKey): array
    {
        $namespace = self::PROVIDER_NAMESPACE_MAP[$providerKey] ?? null;

        if (! $namespace) {
            return [];
        }

        $basePath = base_path("vendor/prism-php/prism/src/Providers/{$namespace}/Maps");

        if (! is_dir($basePath)) {
            return [];
        }

        $supportedTypes = [];

        // Check for ImageMapper
        if (file_exists("{$basePath}/ImageMapper.php")) {
            $supportedTypes[] = 'image';
        }

        // Check for DocumentMapper
        if (file_exists("{$basePath}/DocumentMapper.php")) {
            $supportedTypes[] = 'document';
        }

        // Check for AudioVideoMapper or AudioMapper
        if (file_exists("{$basePath}/AudioVideoMapper.php") || file_exists("{$basePath}/AudioMapper.php")) {
            $supportedTypes[] = 'audio';
        }

        // Check for AudioVideoMapper or VideoMapper
        if (file_exists("{$basePath}/AudioVideoMapper.php") || file_exists("{$basePath}/VideoMapper.php")) {
            $supportedTypes[] = 'video';
        }

        return $supportedTypes;
    }

    public function supportsMediaType(string $providerKey, string $mediaType): bool
    {
        return \in_array($mediaType, $this->getSupportedMediaTypes($providerKey), true);
    }
}
