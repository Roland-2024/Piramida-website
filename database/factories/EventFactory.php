<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+2 months');

        return [
            'status' => ContentStatus::Draft,
            'published_at' => null,
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+2 hours'),
            'external_url' => fake()->optional()->url(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Event $event): void {
            $event->syncTranslations([
                'al' => [
                    'title' => "Eventi {$event->id}",
                    'slug' => "eventi-{$event->id}",
                    'short_description' => 'Përshkrim i shkurtër.',
                    'description' => '<p>Përshkrimi i eventit.</p>',
                    'location' => 'Tiranë',
                ],
                'en' => [
                    'title' => "Event {$event->id}",
                    'slug' => "event-{$event->id}",
                    'short_description' => 'Short description.',
                    'description' => '<p>Event description.</p>',
                    'location' => 'Tirana',
                ],
            ]);
        });
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Published,
            'published_at' => now()->subMinute(),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->subDay(),
        ]);
    }
}
