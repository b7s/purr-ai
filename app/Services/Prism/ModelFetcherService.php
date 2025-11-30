<?php

declare(strict_types=1);

namespace App\Services\Prism;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModelFetcherService
{
    /**
     * @return array<string>
     */
    public function fetchModels(string $provider, string $apiKeyOrUrl): array
    {
        if (empty($apiKeyOrUrl)) {
            return [];
        }

        return match ($provider) {
            'openai' => $this->fetchOpenAIModels($apiKeyOrUrl),
            'anthropic' => $this->fetchAnthropicModels($apiKeyOrUrl),
            'google' => $this->fetchGoogleModels($apiKeyOrUrl),
            'xai' => $this->fetchXAIModels($apiKeyOrUrl),
            'ollama' => $this->fetchOllamaModels($apiKeyOrUrl),
            default => [],
        };
    }

    /**
     * @return array<string>
     */
    private function fetchOpenAIModels(string $apiKey): array
    {
        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->connectTimeout(30)
                ->get('https://api.openai.com/v1/models');

            if (! $response->successful()) {
                return [];
            }

            $models = collect($response->json('data', []))
                ->pluck('id')
                ->filter(fn ($id) => $this->isRelevantOpenAIModel($id))
                ->sort()
                ->values()
                ->toArray();

            return $models;
        } catch (\Throwable $e) {
            Log::warning('ModelFetcherService: Failed to fetch OpenAI models', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function isRelevantOpenAIModel(string $modelId): bool
    {
        $prefixes = ['gpt-', 'o1', 'o3', 'o4', 'chatgpt-'];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($modelId, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string>
     */
    private function fetchAnthropicModels(string $apiKey): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])
                ->timeout(60)
                ->connectTimeout(30)
                ->get('https://api.anthropic.com/v1/models');

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json('data', []))
                ->pluck('id')
                ->sort()
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('ModelFetcherService: Failed to fetch Anthropic models', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @return array<string>
     */
    private function fetchGoogleModels(string $apiKey): array
    {
        try {
            $response = Http::timeout(60)
                ->connectTimeout(30)
                ->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json('models', []))
                ->pluck('name')
                ->map(fn ($name) => str_replace('models/', '', $name))
                ->filter(fn ($name) => str_contains($name, 'gemini'))
                ->sort()
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('ModelFetcherService: Failed to fetch Google models', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @return array<string>
     */
    private function fetchXAIModels(string $apiKey): array
    {
        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->connectTimeout(30)
                ->get('https://api.x.ai/v1/models');

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json('data', []))
                ->pluck('id')
                ->sort()
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('ModelFetcherService: Failed to fetch xAI models', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @return array<string>
     */
    private function fetchOllamaModels(string $url): array
    {
        try {
            $baseUrl = rtrim($url, '/');
            $response = Http::timeout(60)
                ->connectTimeout(30)
                ->get("{$baseUrl}/api/tags");

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json('models', []))
                ->pluck('name')
                ->sort()
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('ModelFetcherService: Failed to fetch Ollama models', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @return array{text: array<string>, image: array<string>, audio: array<string>, video: array<string>}
     */
    public function categorizeModels(string $provider, array $models): array
    {
        $categorized = [
            'text' => [],
            'image' => [],
            'audio' => [],
            'video' => [],
        ];

        foreach ($models as $model) {
            $categories = $this->getModelCategories($provider, $model);
            foreach ($categories as $category) {
                $categorized[$category][] = $model;
            }
        }

        return $categorized;
    }

    /**
     * @return array<string>
     */
    private function getModelCategories(string $provider, string $model): array
    {
        $categories = [];
        $modelLower = strtolower($model);

        if ($provider === 'openai') {
            if (str_contains($modelLower, 'dall-e') || str_contains($modelLower, 'gpt-image')) {
                $categories[] = 'image';
            } elseif (str_contains($modelLower, 'tts') || str_contains($modelLower, 'whisper')) {
                $categories[] = 'audio';
            } elseif (str_starts_with($modelLower, 'gpt-') || str_starts_with($modelLower, 'o1') || str_starts_with($modelLower, 'o3') || str_starts_with($modelLower, 'o4') || str_starts_with($modelLower, 'chatgpt-')) {
                $categories[] = 'text';
            }
        } elseif ($provider === 'google') {
            if (str_contains($modelLower, 'imagen')) {
                $categories[] = 'image';
            } elseif (str_contains($modelLower, 'gemini')) {
                $categories[] = 'text';
            }
        } else {
            $categories[] = 'text';
        }

        if (empty($categories)) {
            $categories[] = 'text';
        }

        return $categories;
    }
}
