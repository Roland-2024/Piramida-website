<?php

namespace Database\Factories;

use App\Enums\BusinessCategory;
use App\Enums\ContentStatus;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(BusinessCategory::cases()),
            'status' => ContentStatus::Draft,
            'display_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Business $business): void {
            $business->syncTranslations([
                'al' => [
                    'name' => "Biznesi {$business->id}",
                    'slug' => "biznesi-{$business->id}",
                    'description' => '<p>Pershkrimi i biznesit.</p>',
                ],
                'en' => [
                    'name' => "Business {$business->id}",
                    'slug' => "business-{$business->id}",
                    'description' => '<p>Business description.</p>',
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
