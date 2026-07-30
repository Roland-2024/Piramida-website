<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => ContentStatus::Draft,
            'published_at' => null,
            'author_name' => fake()->name(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (News $news): void {
            $news->syncTranslations([
                'al' => [
                    'title' => "Lajmi {$news->id}",
                    'slug' => "lajmi-{$news->id}",
                    'excerpt' => 'Përmbledhje e lajmit.',
                    'content' => '<p>Përmbajtja e lajmit.</p>',
                ],
                'en' => [
                    'title' => "News {$news->id}",
                    'slug' => "news-{$news->id}",
                    'excerpt' => 'News excerpt.',
                    'content' => '<p>News content.</p>',
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
