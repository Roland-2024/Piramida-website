<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Attraction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attraction>
 */
class AttractionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => ContentStatus::Draft,
            'display_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Attraction $attraction): void {
            $attraction->syncTranslations([
                'al' => [
                    'title' => "Atraksioni {$attraction->id}",
                    'slug' => "atraksioni-{$attraction->id}",
                    'description' => '<p>Pershkrimi i atraksionit.</p>',
                ],
                'en' => [
                    'title' => "Attraction {$attraction->id}",
                    'slug' => "attraction-{$attraction->id}",
                    'description' => '<p>Attraction description.</p>',
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
