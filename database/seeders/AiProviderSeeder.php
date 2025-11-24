<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiProvider;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'OpenAI GPT-4',
                'provider' => 'openai',
                'api_key' => null,
                'api_url' => 'https://api.openai.com/v1',
                'config' => [
                    'model' => 'gpt-4',
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
                'is_active' => false,
            ],
            [
                'name' => 'Anthropic Claude',
                'provider' => 'anthropic',
                'api_key' => null,
                'api_url' => 'https://api.anthropic.com/v1',
                'config' => [
                    'model' => 'claude-3-opus-20240229',
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
                'is_active' => false,
            ],
            [
                'name' => 'Google Gemini',
                'provider' => 'google',
                'api_key' => null,
                'api_url' => 'https://generativelanguage.googleapis.com/v1',
                'config' => [
                    'model' => 'gemini-pro',
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
                'is_active' => false,
            ],
            [
                'name' => 'Ollama (Local)',
                'provider' => 'ollama',
                'api_key' => null,
                'api_url' => 'http://localhost:11434',
                'config' => [
                    'model' => 'llama2',
                    'temperature' => 0.7,
                ],
                'is_active' => false,
            ],
        ];

        foreach ($providers as $provider) {
            AiProvider::create($provider);
        }
    }
}
