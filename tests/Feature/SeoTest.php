<?php

namespace Tests\Feature;

use App\Enums\BookingMode;
use App\Enums\SpaceType;
use App\Models\Business;
use App\Models\Career;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\Space;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_schema_uses_only_confirmed_address_fields(): void
    {
        $event = Event::factory()->published()->create();
        $event->translations()->where('locale', 'en')->update([
            'location' => 'Venue', 'street_address' => 'Test Street 10',
            'address_locality' => 'Tirana', 'address_country' => 'AL',
        ]);
        $response = $this->get("/en/events/event-$event->id")->assertOk()->assertSee('Test Street 10');
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $response->getContent(), $matches);
        $address = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR)['@graph'][1]['location']['address'];
        $this->assertSame('PostalAddress', $address['@type']);
        $this->assertSame('Test Street 10', $address['streetAddress']);
        $this->assertArrayNotHasKey('postalCode', $address);
    }

    public function test_article_heading_is_an_h1(): void
    {
        $news = News::factory()->published()->create();
        $response = $this->get("/en/news/news-$news->id")->assertOk();
        $this->assertSame(1, preg_match_all('~<h1\b~', $response->getContent()));
    }

    public function test_catalog_dialogs_do_not_add_page_level_headings(): void
    {
        Business::factory()->published()->count(2)->create();
        Career::factory()->published()->count(2)->create(['booking_mode' => BookingMode::Internal]);
        Space::factory()->published()->count(2)->create(['type' => SpaceType::EventSpace, 'booking_mode' => BookingMode::Internal]);
        foreach (['/businesses', '/careers', '/event-space'] as $url) {
            $response = $this->get($url)->assertOk();
            $this->assertSame(1, preg_match_all('~<h1\b~', $response->getContent()), $url);
        }
    }

    public function test_localized_metadata_and_safe_article_schema_use_the_final_domain(): void
    {
        $news = News::factory()->published()->create();
        $news->translations()->where('locale', 'en')->update([
            'seo_title' => 'A special story',
            'seo_description' => 'Description & details </script><script>alert(1)</script>',
        ]);
        $response = $this->get("/en/news/news-$news->id?utm_source=example")->assertOk();
        $response->assertSee('<link rel="canonical" href="https://piramida.edu.al/en/news/news-'.$news->id.'">', false)
            ->assertSee('<link rel="alternate" hreflang="sq" href="https://piramida.edu.al/news/lajmi-'.$news->id.'">', false)
            ->assertSee('A special story | Pyramid of Tirana')
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee('property="og:image" content="https://piramida.edu.al/template/images/piramida_block_1.jpg"', false);
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $response->getContent(), $matches);
        $schema = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('NewsArticle', $schema['@graph'][0]['@type']);
        $this->assertSame('en', $schema['@graph'][0]['inLanguage']);
        $this->assertStringNotContainsString('<script>', $matches[1]);
        $news->translations()->where('locale', 'al')->delete();
        $this->get("/en/news/news-$news->id")->assertOk()
            ->assertDontSee('<link rel="alternate" hreflang="sq"', false);
    }

    public function test_pagination_and_archive_canonicals_preserve_only_content_parameters(): void
    {
        $this->get('/en/events?period=past&page=2&utm_source=example')->assertOk()
            ->assertSee('href="https://piramida.edu.al/en/events?page=2"', false)
            ->assertDontSee('period=past');
        $this->get('/news?page=1&utm_source=example')->assertOk()
            ->assertSee('<link rel="canonical" href="https://piramida.edu.al/news">', false);
    }

    public function test_sitemap_includes_only_public_translations_and_accessible_records(): void
    {
        $public = News::factory()->published()->create();
        $public->translations()->where('locale', 'en')->delete();
        $draft = News::factory()->create();
        $future = News::factory()->published()->create(['published_at' => now()->addDay()]);
        $closed = Career::factory()->published()->create(['deadline' => now()->subDay()]);
        $unavailable = Space::factory()->published()->create(['type' => SpaceType::Leasing, 'is_available' => false]);
        $xml = $this->get('/sitemap.xml')->assertOk()->streamedContent();
        $this->assertNotFalse(simplexml_load_string($xml));
        $this->assertStringContainsString("https://piramida.edu.al/news/lajmi-$public->id</loc>", $xml);
        foreach (["/en/news/news-$public->id", "/news/lajmi-$draft->id", "/news/lajmi-$future->id", "/careers/pozicioni-$closed->id", "/spaces/hapesira-$unavailable->id"] as $path) {
            $this->assertStringNotContainsString($path.'</loc>', $xml);
        }
        $this->assertStringContainsString('https://piramida.edu.al/en/leasing/floors/ground', $xml);
    }

    public function test_indexing_is_disabled_locally_and_enabled_only_on_the_configured_domain(): void
    {
        config(['seo.indexable' => false]);
        $this->get('/')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/robots.txt')->assertSee('Disallow: /', false);
        config(['seo.indexable' => true]);
        $this->get('https://staging.example.test/')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('https://piramida.edu.al/')->assertOk()->assertHeaderMissing('X-Robots-Tag');
        $this->get('https://piramida.edu.al/robots.txt')->assertSee('Sitemap: https://piramida.edu.al/sitemap.xml', false);
        $this->get('https://piramida.edu.al/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_llms_guide_uses_public_localized_pages_and_cms_copy(): void
    {
        $page = Page::factory()->published()->create();
        $page->translations()->where('locale', 'en')->update(['slug' => 'education', 'title' => "Learning [together]\n<b>today</b>"]);
        DB::table('website_texts')->insert(['locale' => 'en', 'key' => 'seo.description', 'text' => 'A CMS-managed introduction.']);
        $response = $this->get('/llms.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('> A CMS-managed introduction.', false)
            ->assertSee('[Learning \\[together\\] today](https://piramida.edu.al/en/education)', false)
            ->assertSee('https://piramida.edu.al/faqja-'.$page->id, false)
            ->assertSee('https://piramida.edu.al/sitemap.xml', false)
            ->assertDontSee('<b>', false)->assertDontSee('localhost')->assertDontSee('/admin');
        $this->assertStringStartsWith('# ', $response->getContent());
        $page->translations()->where('locale', 'al')->delete();
        $this->get('/llms.txt')->assertOk()->assertDontSee('/faqja-'.$page->id, false);
        $page->update(['published_at' => now()->addDay()]);
        $this->get('/llms.txt')->assertOk()->assertDontSee('/en/education', false);
        $page->update(['published_at' => now()->subDay()]);
        $page->delete();
        $this->get('/llms.txt')->assertOk()->assertDontSee('/en/education', false);
    }

    public function test_discovery_files_and_link_work_on_production_without_static_shadows(): void
    {
        config(['seo.indexable' => true]);
        $this->get('https://piramida.edu.al/llms.txt')->assertOk()->assertHeaderMissing('X-Robots-Tag');
        $this->get('https://piramida.edu.al/robots.txt')->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee("User-agent: *\nAllow: /", false)
            ->assertSee('Sitemap: https://piramida.edu.al/sitemap.xml', false);
        $this->get('https://piramida.edu.al/')->assertOk()
            ->assertSee('rel="describedby" href="https://piramida.edu.al/llms.txt"', false);
        foreach (['robots.txt', 'sitemap.xml', 'llms.txt'] as $file) {
            $this->assertFileDoesNotExist(public_path($file), 'Discovery files must stay dynamic.');
        }
    }

    public function test_news_listing_query_count_does_not_grow_per_article(): void
    {
        News::factory()->published()->create();
        $this->get('/news')->assertOk(); // Warm framework/view setup before counting.
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->get('/news')->assertOk();
        $single = count(DB::getQueryLog());
        News::factory()->published()->count(7)->create();
        DB::flushQueryLog();
        $this->get('/news')->assertOk();
        $many = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertSame($single, $many, 'Article rendering must eager-load relations, not add queries per record.');
    }

    public function test_homepage_has_curated_metadata_and_responsive_hero(): void
    {
        Page::factory()->published()->create(['is_homepage' => true]);
        $this->get('/')->assertOk()
            ->assertSee('Piramida e Tiranës | Teknologji, Kulturë &amp; Evente', false)
            ->assertSee('fetchpriority="high"', false)
            ->assertSee('piramida-hero-960.jpg 960w', false);
    }

    public function test_homepage_fallback_does_not_canonicalize_to_another_page(): void
    {
        Page::factory()->published()->create(['is_homepage' => false]);
        $this->get('/')->assertOk()
            ->assertSee('<link rel="canonical" href="https://piramida.edu.al/">', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="https://piramida.edu.al/en">', false);
    }

    public function test_event_schema_uses_stored_dates_without_inventing_ticket_information(): void
    {
        $event = Event::factory()->published()->create();
        $response = $this->get("/en/events/event-$event->id?page=99")->assertOk();
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $response->getContent(), $matches);
        $graph = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR)['@graph'];
        $this->assertSame('Event', $graph[1]['@type']);
        $this->assertSame($event->starts_at->toIso8601String(), $graph[1]['startDate']);
        $this->assertArrayNotHasKey('offers', $graph[1]);
        $this->assertSame("https://piramida.edu.al/en/events/event-$event->id", $graph[1]['url']);
    }
}
