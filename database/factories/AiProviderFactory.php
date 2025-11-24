<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiProvider>
 */
class AiProviderFactory extends Factory
{
    public function definition(): array
    {
        $provider = fake()->randomElement(['openai', 'anthropic', 'google', 'ollama']);

        return [
            'name' => fake()->company().' AI',
            'provider' => $provider,
            'api_key' => $provider === 'ollama' ? null : fake()->uuid(),
            'api_url' => $provider === 'ollama' ? 'http://localhost:11434' : 'https://api.example.com',
            'config' => [
                'model' => fake()->randomElement(['gpt-4', 'claude-3', 'gemini-pro', 'llama2']),
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ],
            'is_active' => fake()->boolean(80),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
