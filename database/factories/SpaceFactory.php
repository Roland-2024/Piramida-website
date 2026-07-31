<?php

namespace Database\Factories;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\SpaceType;
use App\Models\Space;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Space>
 */
class SpaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(SpaceType::cases()),
            'status' => ContentStatus::Draft,
            'booking_mode' => BookingMode::Internal,
            'capacity' => fake()->numberBetween(10, 500),
            'currency' => 'EUR',
            'display_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Space $space): void {
            $space->syncTranslations([
                'al' => [
                    'title' => "Hapesira {$space->id}",
                    'slug' => "hapesira-{$space->id}",
                    'description' => '<p>Pershkrimi i hapesires.</p>',
                ],
                'en' => [
                    'title' => "Space {$space->id}",
                    'slug' => "space-{$space->id}",
                    'description' => '<p>Space description.</p>',
                ],
            ]);
        });
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ContentStatus::Published,
            'published_at' => now()->subMinute(),
        ]);
    }
}
