<?php

namespace Tests\Feature;

use App\Enums\BookingMode;
use App\Enums\SpaceType;
use App\Models\Attraction;
use App\Models\Business;
use App\Models\Career;
use App\Models\Event;
use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Space;
use Database\Seeders\DemoContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_content_is_complete_bilingual_and_idempotent(): void
    {
        // Seeded events have fixed dates; keep this assertion independent of today's date.
        $this->travelTo(Carbon::parse('2026-08-01 12:00:00'));
        Storage::fake('public');

        $this->seed(DemoContentSeeder::class);
        $this->seed(DemoContentSeeder::class);

        $this->assertSame(5, Page::count());
        $this->assertSame(11, PageSection::count());
        $this->assertSame(3, News::count());
        $this->assertSame(3, Event::count());
        $this->assertSame(2, Attraction::count());
        $this->assertSame(4, Business::count());
        $this->assertSame(6, Space::count());
        $this->assertSame(5, Career::count());
        $this->assertSame(14, Media::count());

        $this->assertDatabaseHas('page_translations', ['locale' => 'al', 'slug' => 'rreth-nesh']);
        $this->assertDatabaseHas('page_translations', ['locale' => 'en', 'slug' => 'about-us']);
        $this->assertDatabaseHas('page_translations', ['locale' => 'al', 'slug' => 'edukim']);
        $this->assertDatabaseHas('page_translations', ['locale' => 'en', 'slug' => 'education']);
        $this->assertDatabaseHas('business_translations', ['locale' => 'en', 'slug' => 'mulliri']);

        $this->assertSame(4, Space::query()->where('type', SpaceType::EventSpace)->count());
        $this->assertSame(2, Space::query()->where('type', SpaceType::Leasing)->count());
        $this->assertSame(6, Space::query()->where('booking_mode', BookingMode::Internal)->count());

        Storage::disk('public')->assertExists('media/demo/piramida-front.jpg');
        Storage::disk('public')->assertExists('media/demo/event-glass.jpg');

        $this->get('/en')
            ->assertOk()
            ->assertSee('The space to connect, build and learn')
            ->assertSee('Step into Piramida')
            ->assertSee('Glass Experience: Parent &amp; Child Workshop', false);

        $this->get('/en/spaces?type=leasing')
            ->assertOk()
            ->assertSee('Mix Digital')
            ->assertSee('Request information');
    }

    public function test_draft_template_renders_every_content_type_in_both_languages(): void
    {
        Storage::fake('public');
        $this->seed(DemoContentSeeder::class);

        foreach (['al', 'en'] as $locale) {
            foreach (['', '/news', '/events', '/events?period=past', '/attractions', '/businesses', '/rent-space', '/spaces', '/spaces?type=leasing', '/careers', '/contact'] as $path) {
                $this->get('/'.$locale.$path)->assertOk()->assertSee('id="mobileMenu"', false);
            }

            foreach ([Page::class => 'pages', News::class => 'news', Event::class => 'events', Attraction::class => 'attractions', Business::class => 'businesses', Space::class => 'spaces', Career::class => 'careers'] as $model => $module) {
                foreach ($model::query()->published()->with('translations')->get() as $record) {
                    $slug = $record->translation($locale, false)->slug;
                    $this->get(route("public.{$module}.show", [$locale, $slug]))->assertOk();
                }
            }

            $this->get(route('public.spaces.overview', $locale))
                ->assertSee(route('public.spaces.index', [$locale, 'type' => 'event_space']), false)
                ->assertSee(route('public.spaces.index', [$locale, 'type' => 'leasing']), false);
        }
    }
}
