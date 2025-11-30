<?php

declare(strict_types=1);

namespace App\Services\Prism\Tools;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Tool;

class VideoGenerationTool
{
    public static function make(): ?Tool
    {
        $config = self::getVideoModelConfig();

        if (! $config) {
            return null;
        }

        return (new Tool)
            ->as('generate_video')
            ->for(
                'Generate videos based on text descriptions. Use this tool when the user asks to create, generate, or produce a video. '.
                'Provide detailed descriptions for better results. The generated video will be saved and displayed to the user.'
            )
            ->withParameter(new StringSchema(
                'prompt',
                'Detailed description of the video to generate. Be specific about scenes, actions, style, and duration.'
            ), required: true)
            ->using(fn (string $prompt): string => self::generateVideo($prompt));
    }

    /**
     * @return array{provider: string, model: string, api_key: string}|null
     */
    private static function getVideoModelConfig(): ?array
    {
        $providers = config('purrai.ai_providers', []);

        foreach ($providers as $provider) {
            $configKey = $provider['config_key'];
            $encrypted = $provider['encrypted'];

            $config = $encrypted
                ? Setting::getJsonDecrypted($configKey, [])
                : Setting::getJson($configKey, []);

            $videoModels = $config['models_video'] ?? [];

            if (empty($videoModels)) {
                continue;
            }

            $apiKey = $config['key'] ?? $config['url'] ?? '';

            if (empty($apiKey)) {
                continue;
            }

            return [
                'provider' => $provider['key'],
                'model' => \is_array($videoModels) ? $videoModels[0] : $videoModels,
                'api_key' => $apiKey,
            ];
        }

        return null;
    }

    private static function generateVideo(string $prompt): string
    {
        $config = self::getVideoModelConfig();

        if (! $config) {
            return json_encode([
                'error' => 'No video generation model configured',
                'user_message' => __('chat.tools.video.no_model_configured'),
            ]);
        }

        try {
            $provider = self::getPrismProvider($config['provider']);

            if (! $provider) {
                return json_encode([
                    'error' => 'Invalid provider',
                    'user_message' => __('chat.tools.video.invalid_provider'),
                ]);
            }

            Log::info('VideoGenerationTool: Video generation requested', [
                'prompt' => $prompt,
                'provider' => $config['provider'],
                'model' => $config['model'],
            ]);

            return self::generateVideoDirectApi($config, $prompt);
        } catch (\Throwable $e) {
            Log::error('VideoGenerationTool: Failed to generate video', [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.tools.video.generation_failed'),
            ]);
        }
    }

    private static function generateVideoDirectApi(array $config, string $prompt): string
    {
        return json_encode([
            'error' => 'Video generation is not yet fully supported',
            'user_message' => __('chat.tools.video.not_supported'),
            'details' => [
                'provider' => $config['provider'],
                'model' => $config['model'],
                'note' => 'Video generation will be available when PrismPHP adds native support or when direct API integration is implemented.',
            ],
        ]);
    }

    private static function getPrismProvider(string $providerKey): ?Provider
    {
        return match ($providerKey) {
            'openai' => Provider::OpenAI,
            'anthropic' => Provider::Anthropic,
            'google' => Provider::Gemini,
            'xai' => Provider::XAI,
            'ollama' => Provider::Ollama,
            default => null,
        };
    }
}
