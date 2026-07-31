<?php

namespace Database\Factories;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\ProgramCategory;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(ProgramCategory::cases()),
            'status' => ContentStatus::Draft,
            'booking_mode' => BookingMode::Internal,
            'display_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Program $program): void {
            $program->syncTranslations([
                'al' => [
                    'title' => "Programi {$program->id}",
                    'slug' => "programi-{$program->id}",
                    'short_description' => 'Pershkrim i shkurter.',
                    'description' => '<p>Pershkrimi i programit.</p>',
                ],
                'en' => [
                    'title' => "Program {$program->id}",
                    'slug' => "program-{$program->id}",
                    'short_description' => 'Short description.',
                    'description' => '<p>Program description.</p>',
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
