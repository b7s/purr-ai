<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'ai_provider_id' => null,
            'model' => null,
        ];
    }

    public function withProvider(): static
    {
        return $this->state(fn (array $attributes) => [
            'ai_provider_id' => AiProvider::factory(),
            'model' => 'gpt-4',
        ]);
    }
}
