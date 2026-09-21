<?php

namespace Tests\Feature;

use App\Enums\BookingMode;
use App\Enums\SectionType;
use App\Models\Career;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use Database\Seeders\PresentationPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PresentationTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_template_uses_published_localized_pages_and_active_ordered_sections(): void
    {
        foreach (['education', 'innovation', 'business', 'art'] as $slug) {
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

            foreach (['al' => "faqja-{$page->id}", 'en' => $slug] as $locale => $localizedSlug) {
                $this->get(route('public.pages.show', [$locale, $localizedSlug]))
                    ->assertOk()->assertViewIs('public.pages.education')
                    ->assertViewHas('slides', fn ($slides) => $slides->count() === 2 && $slides[0]['title'] === "Seksioni {$first->id}")
                    ->assertDontSee("Seksioni {$hidden->id}")->assertDontSee("Section {$hidden->id}");
                $this->get('/'.$locale)->assertSee(route('public.pages.show', [$locale, $localizedSlug]), false);
            }

            $page->update(['status' => 'draft']);
            $this->get(route('public.pages.show', ['en', $slug]))->assertNotFound();
            $this->get('/en')->assertDontSee('href="'.route('public.pages.show', ['en', $slug]).'"', false);
        }
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
        $this->assertSame(14, PageSection::count());
        $this->assertSame(7, Media::count());
        $this->assertSame('draft', $draft->fresh()->status->value);
        $this->assertTrue($deleted->fresh()->trashed());
        $this->get(route('public.pages.show', ['en', 'innovation']))->assertOk()->assertSee('Graphic Design');
        $this->get(route('public.pages.show', ['al', 'art-kulture']))->assertOk()->assertSee('Dizajn grafik');
    }

    public function test_career_popup_keeps_unique_fields_and_reopens_only_the_invalid_application(): void
    {
        $first = Career::factory()->published()->create(['booking_mode' => BookingMode::Internal]);
        $second = Career::factory()->published()->create(['booking_mode' => BookingMode::Internal]);
        $external = Career::factory()->published()->create(['booking_mode' => BookingMode::External]);

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
