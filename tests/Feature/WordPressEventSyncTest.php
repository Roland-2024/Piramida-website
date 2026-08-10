<?php

namespace Tests\Feature;

use App\Actions\SynchronizeWordPressEvents;
use App\Enums\ContentStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WordPressEventSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_synchronizes_bilingual_events_without_touching_manual_events(): void
    {
        Storage::fake('public');
        config([
            'filesystems.default' => 'public',
            'services.wordpress_events.url' => 'https://wordpress.test/wp-json',
            'services.wordpress_events.api_key' => 'test-key',
        ]);

        $manual = Event::factory()->published()->create();
        $posts = [
            'al' => [$this->wordPressPost(101, 'al', 'Koncert në Piramidë')],
            'en' => [$this->wordPressPost(202, 'en', 'Concert at Piramida')],
        ];

        Http::fake(function (Request $request) use (&$posts) {
            if ($request->url() === 'https://cdn.test/event.png') {
                return Http::response(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='), 200, [
                    'Content-Type' => 'image/png',
                ]);
            }

            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return Http::response($posts[$query['lang']] ?? [], 200, ['X-WP-TotalPages' => '1']);
        });

        $first = app(SynchronizeWordPressEvents::class)->handle();

        $this->assertSame(1, $first['created']);
        $this->assertSame(0, $first['skipped']);
        $this->assertDatabaseCount('events', 2);
        $this->assertDatabaseHas('event_translations', [
            'wordpress_id' => 101,
            'locale' => 'al',
            'title' => 'Koncert në Piramidë',
        ]);
        $this->assertDatabaseHas('event_translations', [
            'wordpress_id' => 202,
            'locale' => 'en',
            'title' => 'Concert at Piramida',
        ]);

        $imported = Event::query()
            ->whereHas('translations', fn ($query) => $query->whereNotNull('wordpress_id'))
            ->with(['translations', 'featuredMedia'])
            ->firstOrFail();

        $this->assertSame(ContentStatus::Published, $imported->status);
        $this->assertTrue($imported->is_featured);
        $this->assertSame('https://tickets.test/event', $imported->external_url);
        $this->assertSame('Tirana', $imported->translation('en')->location);
        $this->assertStringContainsString('Organizer', $imported->translation('en')->description);
        $this->assertNotNull($imported->featuredMedia);
        Storage::disk('public')->assertExists($imported->featuredMedia->path);

        $second = app(SynchronizeWordPressEvents::class)->handle();

        $this->assertSame(0, $second['created']);
        $this->assertSame(1, $second['updated']);
        $this->assertDatabaseCount('events', 2);
        $this->assertSame(ContentStatus::Published, $manual->fresh()->status);

        Http::assertSent(fn (Request $request): bool => ! str_contains($request->url(), 'wp/v2/event')
            || $request->hasHeader('Api-Key', 'test-key'));

        $posts = ['al' => [], 'en' => []];
        $removed = app(SynchronizeWordPressEvents::class)->handle();

        $this->assertSame(1, $removed['drafted']);
        $this->assertSame(ContentStatus::Draft, $imported->fresh()->status);
        $this->assertSame(ContentStatus::Published, $manual->fresh()->status);
    }

    public function test_an_editor_can_start_a_manual_wordpress_sync(): void
    {
        config([
            'services.wordpress_events.url' => 'https://wordpress.test/wp-json',
            'services.wordpress_events.api_key' => 'test-key',
        ]);

        Http::fake([
            'https://wordpress.test/wp-json/wp/v2/event*' => Http::response([], 200, ['X-WP-TotalPages' => '1']),
        ]);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.events.sync-wordpress'))
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    /**
     * @return array<string, mixed>
     */
    private function wordPressPost(int $id, string $locale, string $title): array
    {
        return [
            'id' => $id,
            'date' => '2026-08-01T10:00:00',
            'date_gmt' => '2026-08-01T08:00:00',
            'slug' => $locale === 'al' ? 'koncert-ne-piramide' : 'concert-at-piramida',
            'status' => 'publish',
            'title' => ['rendered' => $title],
            'content' => ['rendered' => '<p>Event content.</p>'],
            'excerpt' => ['rendered' => '<p>Short event text.</p>'],
            'featured_image_url' => 'https://cdn.test/event.png',
            'event_categories' => [['slug' => 'featured']],
            'meta' => [
                '_event_start_date' => '2026-09-10 18:00:00',
                '_event_end_date' => $locale === 'al' ? '2026-09-10 20:00:00' : '',
                '_event_organizer' => $locale === 'al' ? 'Piramida' : 'Piramida',
                '_event_duration' => '2 hours',
                '_event_location' => $locale === 'al' ? 'Tiranë' : 'Tirana',
                '_event_agenda' => 'https://agenda.test/event',
                '_event_join_url' => 'https://tickets.test/event',
            ],
        ];
    }
}
