<?php

namespace Database\Factories;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageSection>
 */
class PageSectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'internal_name' => fake()->words(3, true),
            'type' => fake()->randomElement(SectionType::cases()),
            'display_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
            'structured_data' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (PageSection $section): void {
            $section->syncTranslations([
                'al' => ['title' => "Seksioni {$section->id}", 'description' => '<p>Përshkrimi.</p>'],
                'en' => ['title' => "Section {$section->id}", 'description' => '<p>Description.</p>'],
            ]);
        });
    }
}
