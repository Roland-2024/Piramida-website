<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => ContentStatus::Draft,
            'is_homepage' => false,
            'published_at' => null,
            'display_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Page $page): void {
            $page->syncTranslations([
                'al' => [
                    'title' => "Faqja {$page->id}",
                    'slug' => "faqja-{$page->id}",
                    'short_description' => 'Përshkrim i shkurtër.',
                    'content' => '<p>Përmbajtja e faqes.</p>',
                ],
                'en' => [
                    'title' => "Page {$page->id}",
                    'slug' => "page-{$page->id}",
                    'short_description' => 'Short description.',
                    'content' => '<p>Page content.</p>',
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
}
