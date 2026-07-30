<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word().'.jpg';

        return [
            'disk' => 'public',
            'path' => 'media/'.$name,
            'original_name' => $name,
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 1024,
            'width' => 1200,
            'height' => 800,
            'alt_text_al' => fake()->sentence(3),
            'alt_text_en' => fake()->sentence(3),
        ];
    }
}
