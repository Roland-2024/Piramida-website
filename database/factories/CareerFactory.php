<?php

namespace Database\Factories;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\EmploymentType;
use App\Models\Career;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Career>
 */
class CareerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'status' => ContentStatus::Draft,
            'booking_mode' => BookingMode::Internal,
            'deadline' => now()->addMonth(),
            'display_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Career $career): void {
            $career->syncTranslations([
                'al' => [
                    'title' => "Pozicioni {$career->id}",
                    'slug' => "pozicioni-{$career->id}",
                    'description' => '<p>Pershkrimi i pozicionit.</p>',
                ],
                'en' => [
                    'title' => "Position {$career->id}",
                    'slug' => "position-{$career->id}",
                    'description' => '<p>Position description.</p>',
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
