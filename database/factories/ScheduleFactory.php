<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Zap\Enums\ScheduleTypes;
use Zap\Models\Schedule;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'type' => fake()->randomElement(ScheduleTypes::cases()),
        ];
    }
}
