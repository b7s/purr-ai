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

class ImageGenerationTool
{
    public static function make(): ?Tool
    {
        $config = self::getImageModelConfig();

        if (! $config) {
            return null;
        }

        return (new Tool)
            ->as('generate_image')
            ->for(
                'Generate images based on text descriptions. Use this tool when the user asks to create, generate, or draw an image. ' .
                    'Provide detailed descriptions for better results. The generated image will be saved and displayed to the user.'
            )
            ->withParameter(new StringSchema(
                'prompt',
                'Detailed description of the image to generate. Be specific about style, colors, composition, and subject matter.'
            ), required: true)
            ->withParameter(new EnumSchema(
                'size',
                'Image size/dimensions',
                ['1024x1024', '1024x1536', '1536x1024', 'auto']
            ), required: false)
            ->using(function (string $prompt, ?string $size = null): string {
                return self::generateImage($prompt, $size);
            });
    }

    /**
     * @return array{provider: string, model: string, api_key: string}|null
     */
    private static function getImageModelConfig(): ?array
    {
        $providers = config('purrai.ai_providers', []);

        foreach ($providers as $provider) {
            $configKey = $provider['config_key'];
            $encrypted = $provider['encrypted'];

            $config = $encrypted
                ? Setting::getJsonDecrypted($configKey, [])
                : Setting::getJson($configKey, []);

            $imageModels = $config['models_image'] ?? [];

            if (empty($imageModels)) {
                continue;
            }

            $apiKey = $config['key'] ?? $config['url'] ?? '';

            if (empty($apiKey)) {
                continue;
            }

            return [
                'provider' => $provider['key'],
                'model' => is_array($imageModels) ? $imageModels[0] : $imageModels,
                'api_key' => $apiKey,
            ];
        }

        return null;
    }

    private static function generateImage(string $prompt, ?string $size): string
    {
        $config = self::getImageModelConfig();

        if (! $config) {
            return json_encode([
                'error' => 'No image generation model configured',
                'user_message' => __('chat.tools.image.no_model_configured'),
            ]);
        }

        try {
            $provider = self::getPrismProvider($config['provider']);

            if (! $provider) {
                return json_encode([
                    'error' => 'Invalid provider',
                    'user_message' => __('chat.tools.image.invalid_provider'),
                ]);
            }

            $request = Prism::image()
                ->using($provider, $config['model'], ['api_key' => $config['api_key']])
                ->withPrompt($prompt)
                ->withClientOptions([
                    'timeout' => config('purrai.limits.timeout'),
                    'connect_timeout' => 30,
                ]);

            if ($size) {
                $request->withProviderOptions(['size' => $size]);
            }

            $response = $request->generate();

            $images = $response->images;

            if (empty($images)) {
                return json_encode([
                    'error' => 'No image generated',
                    'user_message' => __('chat.tools.image.generation_failed'),
                ]);
            }

            $savedImages = [];

            foreach ($images as $index => $image) {
                $filename = 'generated_' . time() . '_' . $index . '.png';
                $path = "generated_images/{$filename}";

                if ($image->base64) {
                    Storage::put($path, base64_decode($image->base64));
                    $publicUrl = route('media.serve', ['path' => $path]);
                    $savedImages[] = [
                        'path' => $path,
                        'url' => $publicUrl,
                        'type' => 'image',
                        'revised_prompt' => $image->revisedPrompt,
                    ];
                } elseif ($image->url) {
                    $savedImages[] = [
                        'url' => $image->url,
                        'type' => 'image',
                        'revised_prompt' => $image->revisedPrompt,
                    ];
                }
            }

            return json_encode([
                'success' => true,
                'media' => $savedImages,
                'user_message' => __('chat.tools.image.generated', ['count' => \count($savedImages)]),
            ]);
        } catch (\Throwable $e) {
            Log::error('ImageGenerationTool: Failed to generate image', [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.tools.image.generation_failed'),
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
