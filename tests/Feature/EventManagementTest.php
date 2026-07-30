<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_upcoming_and_past_scopes_use_the_event_end_time(): void
    {
        $upcoming = Event::factory()->create([
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(2),
        ]);
        $past = Event::factory()->past()->create();

        $this->assertSame([$upcoming->id], Event::query()->upcoming()->pluck('id')->all());
        $this->assertSame([$past->id], Event::query()->past()->pluck('id')->all());
    }

    public function test_event_end_time_cannot_be_before_start_time(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.events.store'), [
                'status' => ContentStatus::Draft->value,
                'published_at' => null,
                'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'ends_at' => now()->format('Y-m-d H:i:s'),
                'external_url' => null,
                'featured_media_id' => null,
                'translations' => [
                    'al' => [
                        'title' => 'Eventi',
                        'slug' => '',
                        'short_description' => null,
                        'description' => null,
                        'location' => 'Tiranë',
                        'seo_title' => null,
                        'seo_description' => null,
                    ],
                    'en' => [
                        'title' => 'Event',
                        'slug' => '',
                        'short_description' => null,
                        'description' => null,
                        'location' => 'Tirana',
                        'seo_title' => null,
                        'seo_description' => null,
                    ],
                ],
            ])
            ->assertSessionHasErrors('ends_at');
    }
}
