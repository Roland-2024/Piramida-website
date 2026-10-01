<?php

namespace Tests\Feature;

use App\Enums\BookingMode;
use App\Models\Event;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_canonical_urls_and_language_switches_use_localized_slugs(): void
    {
        $page = Page::factory()->published()->create();
        $this->assertSame(url('/'), route('public.home', 'al'));
        $this->assertSame(url('/en'), route('public.home', 'en'));
        $this->assertSame(url('/leasing'), route('public.leasing.index', 'al'));
        $this->assertSame('/leasing', route('public.leasing.index', 'al', false));
        $this->get("/faqja-$page->id")->assertOk()->assertSee('lang="sq"', false)
            ->assertSee('href="'.url("/en/page-$page->id").'"', false);
        $this->get("/en/page-$page->id")->assertOk()->assertSee('lang="en"', false)
            ->assertSee('href="'.url("/faqja-$page->id").'"', false);
        $this->get("/page-$page->id")->assertNotFound();
        $this->get("/en/faqja-$page->id")->assertNotFound();
        $this->get('/leasing/floors/ground')->assertOk()->assertSee('Kati 0');
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/admin/login')->assertOk();
    }

    public function test_legacy_links_redirect_permanently_without_losing_filters(): void
    {
        $this->get('/al')->assertStatus(301)->assertRedirect('/');
        $this->get('/al/events?period=past&page=2')->assertStatus(301)
            ->assertRedirect('/events?page=2&period=past');
        $this->get('/al/leasing/floors/ground')->assertStatus(301)->assertRedirect('/leasing/floors/ground');
        $this->get('/spaces?type=leasing')->assertStatus(301)->assertRedirect('/leasing');
        $this->get('/en/spaces?type=leasing')->assertStatus(301)->assertRedirect('/en/leasing');
    }

    public function test_albanian_post_routes_keep_form_specific_validation(): void
    {
        foreach (['/contact', '/al/contact', '/en/contact'] as $url) {
            $this->post($url, [])->assertSessionHasErrors(['name', 'email', 'message']);
        }
        $this->post('/spaces/not-available/leasing-request', [])->assertNotFound();
        $this->post('/careers/example/apply', [])->assertSessionHasErrors(['first_name', 'last_name', 'attachment']);
        $this->post('/spaces/example/event-request', [])->assertSessionHasErrors(['event_type', 'preferred_date', 'attendees']);
    }

    public function test_albanian_event_submission_resolves_the_correct_localized_record(): void
    {
        $event = Event::factory()->published()->create(['booking_mode' => BookingMode::Internal]);
        $slug = $event->translation('al', false)->slug;
        $this->post("/events/$slug/request", [
            'name' => 'Test visitor', 'email' => 'visitor@example.test',
            'phone' => '+355691234567', 'privacy' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'related_type' => Event::class, 'related_id' => $event->id, 'name' => 'Test visitor',
        ]);
    }
}
