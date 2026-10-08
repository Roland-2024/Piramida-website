<?php

namespace Tests\Feature;

use App\Enums\BookingMode;
use App\Enums\SectionType;
use App\Enums\SpaceType;
use App\Models\Business;
use App\Models\Career;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Program;
use App\Models\Space;
use Database\Seeders\CarouselProgramSeeder;
use Database\Seeders\PresentationPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PresentationTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_template_uses_imported_posts_and_hides_missing_translations(): void
    {
        foreach (['education', 'innovation', 'art'] as $slug) {
            $page = Page::factory()->published()->create();
            $page->translations()->where('locale', 'en')->update(['slug' => $slug]);
            $first = PageSection::factory()->for($page)->create([
                'type' => SectionType::TextImage, 'display_order' => 1,
                'primary_media_id' => Media::factory(),
            ]);
            $last = PageSection::factory()->for($page)->create([
                'type' => SectionType::Gallery, 'display_order' => 2,
            ]);
            $last->gallery()->attach(Media::factory()->create()->id);
            $hidden = PageSection::factory()->for($page)->create([
                'type' => SectionType::TextImage, 'is_active' => false,
                'primary_media_id' => Media::factory(),
            ]);
            $first->translations()->where('locale', 'en')->delete();
            $this->seed(CarouselProgramSeeder::class);

            foreach (['al' => "faqja-{$page->id}", 'en' => $slug] as $locale => $localizedSlug) {
                $this->get(route('public.pages.show', [$locale, $localizedSlug]))
                    ->assertOk()->assertViewIs('public.pages.education')
                    ->assertViewHas('slides', fn ($slides) => $slides->count() === ($locale === 'al' ? 2 : 1)
                        && $slides[0]['title'] === ($locale === 'al' ? "Seksioni {$first->id}" : "Section {$last->id}"))
                    ->assertDontSee("Seksioni {$hidden->id}")->assertDontSee("Section {$hidden->id}");
                $this->get(route('public.home', $locale))->assertSee(route('public.pages.show', [$locale, $localizedSlug]), false);
            }

            $page->update(['status' => 'draft']);
            $this->get(route('public.pages.show', ['en', $slug]))->assertNotFound();
            $this->get('/en')->assertDontSee('href="'.route('public.pages.show', ['en', $slug]).'"', false);
        }
    }

    public function test_business_presentation_uses_only_published_localized_business_records(): void
    {
        $page = Page::factory()->published()->create();
        $page->translations()->where('locale', 'en')->update(['slug' => 'business']);
        $business = Business::factory()->published()->create(['featured_media_id' => Media::factory()]);
        Business::factory()->create();
        Business::factory()->published()->create(['published_at' => now()->addDay()]);
        $untranslated = Business::factory()->published()->create();
        $untranslated->translations()->where('locale', 'en')->delete();

        $this->get('/en/business')->assertOk()->assertViewIs('public.pages.education')
            ->assertViewHas('slides', fn ($slides) => $slides->count() === 1
                && $slides[0]['title'] === "Business {$business->id}"
                && $slides[0]['url'] === $business->featuredMedia->url());
    }

    public function test_template_seed_adds_missing_pages_without_overwriting_drafts_or_deleted_pages(): void
    {
        Storage::fake('public');
        $draft = Page::factory()->create();
        $draft->translations()->where('locale', 'en')->update(['slug' => 'education']);
        $deleted = Page::factory()->published()->create();
        $deleted->translations()->where('locale', 'en')->update(['slug' => 'business']);
        $deleted->delete();

        $this->seed(PresentationPageSeeder::class);
        $this->seed(PresentationPageSeeder::class);

        $this->assertSame(4, Page::withTrashed()->count());
        $this->assertSame(0, PageSection::count());
        $this->assertSame(14, Program::count());
        $this->assertSame(7, Media::count());
        $this->assertSame('draft', $draft->fresh()->status->value);
        $this->assertTrue($deleted->fresh()->trashed());
        $this->get(route('public.pages.show', ['en', 'innovation']))->assertOk()->assertSee('Graphic Design');
        $this->get(route('public.pages.show', ['al', 'art-kulture']))->assertOk()->assertSee('Dizajn grafik');
    }

    public function test_space_booking_opens_per_record_dialog_and_reopens_only_invalid_form(): void
    {
        $first = Space::factory()->published()->create(['type' => SpaceType::EventSpace, 'booking_mode' => BookingMode::Internal]);
        $second = Space::factory()->published()->create(['type' => SpaceType::EventSpace, 'booking_mode' => BookingMode::Internal]);
        $external = Space::factory()->published()->create(['type' => SpaceType::EventSpace, 'booking_mode' => BookingMode::External]);
        $slug = $second->translation('en', false)->slug;
        $this->from('/en/event-space')->post(route('public.spaces.event-request', ['en', $slug]), [
            '_space_id' => (string) $second->id, 'email' => 'invalid',
        ])->assertRedirect('/en/event-space')->assertSessionHasErrors('email');
        $response = $this->withCookie(config('session.cookie'), session()->getId())->get('/en/event-space')
            ->assertOk()->assertSee('data-dialog-open="space-request-'.$first->id.'"', false)
            ->assertSee('data-dialog-open="space-request-'.$second->id.'"', false)
            ->assertDontSee('data-dialog-open="space-request-'.$external->id.'"', false)
            ->assertSee('id="space-'.$first->id.'-email"', false)
            ->assertSee('id="space-'.$second->id.'-email"', false);
        $this->assertSame(1, substr_count($response->getContent(), 'data-feedback="true"'));
        $this->assertSame(4, substr_count($response->getContent(), 'data-space-target='));
        $response->assertSee('data-space-target="event-spaces-intro"', false)
            ->assertSee('data-space-target="space-3"', false);
    }

    public function test_career_popup_keeps_unique_fields_and_reopens_only_the_invalid_application(): void
    {
        $first = Career::factory()->published()->create(['booking_mode' => BookingMode::Internal]);
        $second = Career::factory()->published()->create(['booking_mode' => BookingMode::Internal]);
        $external = Career::factory()->published()->create(['booking_mode' => BookingMode::External]);

        $this->get('/en/careers')->assertOk()->assertDontSee('class="role-card rounded-2xl" open', false);

        $this->from('/en/careers')->post(route('public.careers.apply', ['en', "position-{$second->id}"]), [
            '_career_id' => (string) $second->id,
            'email' => 'invalid',
        ])->assertRedirect('/en/careers')->assertSessionHasErrors('email')
            ->assertSessionHasInput('_career_id', (string) $second->id);

        $response = $this->withCookie(config('session.cookie'), session()->getId())->get('/en/careers')
            ->assertOk()
            ->assertSee('data-dialog-open="career-'.$first->id.'"', false)
            ->assertSee('data-dialog-open="career-'.$second->id.'"', false)
            ->assertDontSee('data-dialog-open="career-'.$external->id.'"', false)
            ->assertSee('id="career-'.$first->id.'-email"', false)
            ->assertSee('id="career-'.$second->id.'-email"', false)
            ->assertSee(route('public.careers.apply', ['en', "position-{$second->id}"]), false);
        $this->assertSame(1, substr_count($response->getContent(), 'data-feedback="true"'));
    }
}
