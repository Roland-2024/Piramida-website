<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_albanian_and_invalid_locale_returns_404(): void
    {
        $this->get('/')->assertRedirect('/al');
        $this->get('/fr')->assertNotFound();
    }

    public function test_published_page_resolves_only_by_the_active_locale_slug(): void
    {
        $page = Page::factory()->published()->create();

        $this->get(route('public.pages.show', ['en', "page-{$page->id}"]))
            ->assertOk()
            ->assertSee("Page {$page->id}");

        $this->get(route('public.pages.show', ['en', "faqja-{$page->id}"]))
            ->assertNotFound();
    }

    public function test_draft_and_future_dated_pages_are_not_public(): void
    {
        $draft = Page::factory()->create();
        $future = Page::factory()->create([
            'status' => ContentStatus::Published,
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('public.pages.show', ['en', "page-{$draft->id}"]))->assertNotFound();
        $this->get(route('public.pages.show', ['en', "page-{$future->id}"]))->assertNotFound();
    }

    public function test_public_page_renders_only_active_sections_in_display_order_with_fallback(): void
    {
        $page = Page::factory()->published()->create();
        $last = PageSection::factory()->for($page)->create(['display_order' => 20]);
        $first = PageSection::factory()->for($page)->create(['display_order' => 5]);
        $inactive = PageSection::factory()->for($page)->create(['display_order' => 1, 'is_active' => false]);
        $first->translations()->where('locale', 'en')->delete();

        $this->get(route('public.pages.show', ['en', "page-{$page->id}"]))
            ->assertOk()
            ->assertSeeInOrder(["Seksioni {$first->id}", "Section {$last->id}"])
            ->assertDontSee("Section {$inactive->id}");
    }

    public function test_news_listing_and_detail_obey_publication_dates(): void
    {
        $visible = News::factory()->published()->create();
        $future = News::factory()->create([
            'status' => ContentStatus::Published,
            'published_at' => now()->addDay(),
        ]);
        $draft = News::factory()->create();

        $this->get(route('public.news.index', 'en'))
            ->assertOk()
            ->assertSee("News {$visible->id}")
            ->assertDontSee("News {$future->id}")
            ->assertDontSee("News {$draft->id}");

        $this->get(route('public.news.show', ['en', "news-{$visible->id}"]))->assertOk();
        $this->get(route('public.news.show', ['en', "news-{$future->id}"]))->assertNotFound();
    }

    public function test_event_listing_separates_upcoming_and_past_published_events(): void
    {
        $upcoming = Event::factory()->published()->create();
        $past = Event::factory()->published()->past()->create();
        $draft = Event::factory()->create();

        $this->get(route('public.events.index', 'en'))
            ->assertOk()
            ->assertSee("Event {$upcoming->id}")
            ->assertDontSee("Event {$past->id}")
            ->assertDontSee("Event {$draft->id}");

        $this->get(route('public.events.index', ['en', 'period' => 'past']))
            ->assertOk()
            ->assertSee("Event {$past->id}")
            ->assertDontSee("Event {$upcoming->id}");
    }

    public function test_language_switch_uses_the_corresponding_localized_slug(): void
    {
        $page = Page::factory()->published()->create();

        $this->get(route('public.pages.show', ['en', "page-{$page->id}"]))
            ->assertOk()
            ->assertSee(route('public.pages.show', ['al', "faqja-{$page->id}"]), false);
    }
}
