<?php

declare(strict_types=1);

namespace App\Services\Prism\Tools;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Tool;

class AudioGenerationTool
{
    public static function make(): ?Tool
    {
        $config = self::getAudioModelConfig();

        if (! $config) {
            return null;
        }

        return (new Tool)
            ->as('generate_audio')
            ->for(
                'Generate audio/speech from text. Use this tool when the user asks to create audio, generate speech, or convert text to speech. '.
                'The generated audio will be saved and displayed to the user. If the user does not specify the voice, use "alloy" voice.'
            )
            ->withParameter(new StringSchema(
                'text',
                'The text to convert to speech/audio.'
            ), required: true)
            ->withParameter(new EnumSchema(
                'voice',
                'Voice to use for speech generation',
                ['alloy', 'echo', 'fable', 'onyx', 'nova', 'shimmer']
            ), required: false)
            ->using(fn (string $text, ?string $voice = null): string => self::generateAudio($text, $voice));
    }

    /**
     * @return array{provider: string, model: string, api_key: string}|null
     */
    private static function getAudioModelConfig(): ?array
    {
        $providers = config('purrai.ai_providers', []);

        foreach ($providers as $provider) {
            $configKey = $provider['config_key'];
            $encrypted = $provider['encrypted'];

            $config = $encrypted
                ? Setting::getJsonDecrypted($configKey, [])
                : Setting::getJson($configKey, []);

            $audioModels = $config['models_audio'] ?? [];

            if (empty($audioModels)) {
                continue;
            }

            $apiKey = $config['key'] ?? $config['url'] ?? '';

            if (empty($apiKey)) {
                continue;
            }

            return [
                'provider' => $provider['key'],
                'model' => \is_array($audioModels) ? $audioModels[0] : $audioModels,
                'api_key' => $apiKey,
            ];
        }

        return null;
    }

    private static function generateAudio(string $text, ?string $voice): string
    {
        $config = self::getAudioModelConfig();

        if (! $config) {
            return json_encode([
                'error' => 'No audio generation model configured',
                'user_message' => __('chat.tools.audio.no_model_configured'),
            ]);
        }

        try {
            $provider = self::getPrismProvider($config['provider']);

            if (! $provider) {
                return json_encode([
                    'error' => 'Invalid provider',
                    'user_message' => __('chat.tools.audio.invalid_provider'),
                ]);
            }

            $request = Prism::audio()
                ->using($provider, $config['model'], ['api_key' => $config['api_key']])
                ->withInput($text);

            if ($voice) {
                $request->withVoice($voice);
            }

            $response = $request->asAudio();

            $audioData = $response->audio->rawContent();

            if (empty($audioData)) {
                return json_encode([
                    'error' => 'No audio generated',
                    'user_message' => __('chat.tools.audio.generation_failed'),
                ]);
            }

            $filename = 'generated_'.time().'.mp3';
            $path = "generated_audio/{$filename}";

            Storage::put($path, $audioData);
            $publicUrl = route('media.serve', ['path' => $path]);

            $savedAudio = [
                'path' => $path,
                'url' => $publicUrl,
                'type' => 'audio',
            ];

            Log::info('AudioGenerationTool: Audio generated successfully', [
                'text' => $text,
            ]);

            return json_encode([
                'success' => true,
                'media' => [$savedAudio],
                'user_message' => __('chat.tools.audio.generated'),
            ]);
        } catch (\Throwable $e) {
            Log::error('AudioGenerationTool: Failed to generate audio', [
                'text' => $text,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.tools.audio.generation_failed'),
            ]);
        }
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
