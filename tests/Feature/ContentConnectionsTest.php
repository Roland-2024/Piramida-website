<?php

namespace Tests\Feature;

use App\Enums\SectionType;
use App\Models\Attraction;
use App\Models\Business;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use App\Support\WebsiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ContentConnectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_promotions_use_editor_saved_sections_without_changing_catalogue_records(): void
    {
        $this->actingAs(User::factory()->create());
        $page = Page::factory()->published()->create(['is_homepage' => true]);
        $attraction = Attraction::factory()->published()->create(['is_featured' => true]);
        $originalTitle = $attraction->translation('en', false)->title;
        $sections = [];
        foreach (['Homepage - Attraction', 'Homepage - Experiences'] as $name) {
            $section = PageSection::factory()->for($page)->create(['internal_name' => $name]);
            $image = Media::factory()->create();
            $translations = [];
            foreach (['al', 'en'] as $locale) {
                $translations[$locale] = [
                    'title' => "$locale card $section->id title", 'subtitle' => "$locale card $section->id heading",
                    'description' => "<p>$locale card $section->id body</p>",
                    'primary_button_label' => "$locale card $section->id action",
                    'secondary_button_label' => "$locale card $section->id secondary",
                ];
            }
            $this->put(route('admin.sections.update', $section), [
                'page_id' => $page->id, 'internal_name' => $name, 'type' => 'text_image',
                'display_order' => 10, 'is_active' => true, 'primary_media_id' => $image->id,
                'primary_button_url' => '/events', 'secondary_button_url' => '/contact', 'translations' => $translations,
            ])->assertSessionHasNoErrors()->assertRedirect();
            $sections[] = $section;
            foreach (['/' => 'al', '/en' => 'en'] as $url => $locale) {
                $this->get($url)->assertOk()->assertSee("$locale card $section->id title")
                    ->assertSee("$locale card $section->id heading")->assertSee("$locale card $section->id body")
                    ->assertSee("$locale card $section->id action")->assertSee("$locale card $section->id secondary")
                    ->assertSee($image->url(), false);
            }
            $this->get(route('admin.sections.show', $section))->assertSee('promotional card');
            $this->put(route('admin.sections.update', $section), [
                'page_id' => $page->id, 'internal_name' => 'Renamed section', 'type' => 'text_image',
                'display_order' => 10, 'is_active' => true, 'translations' => $translations,
            ])->assertSessionHasErrors('internal_name');
        }
        $this->assertSame($originalTitle, $attraction->fresh()->translation('en', false)->title);
        foreach ($sections as $section) {
            $section->update(['is_active' => false]);
        }
        $this->get('/en')->assertOk()->assertDontSee('data-home-promotion=', false);
        $this->assertSame('en card '.$sections[0]->id.' title', $sections[0]->fresh()->translation('en', false)->title);
    }

    public function test_footer_partners_use_published_homepage_section_and_ordered_gallery_across_pages(): void
    {
        $this->actingAs(User::factory()->create());
        $page = Page::factory()->published()->create(['is_homepage' => true]);
        $partners = PageSection::factory()->for($page)->create(['internal_name' => 'Homepage - Partners']);
        $first = Media::factory()->create(['path' => 'media/first-partner.png']);
        $second = Media::factory()->create(['path' => 'media/second-partner.png']);
        $this->put(route('admin.sections.update', $partners), [
            'page_id' => $page->id, 'internal_name' => $partners->internal_name, 'type' => 'partners',
            'display_order' => 50, 'is_active' => true, 'gallery_media_ids' => [$second->id, $first->id],
            'translations' => [
                'al' => ['title' => 'Partnerë të zgjedhur', 'subtitle' => 'Bashkëpunim', 'description' => '<p>Partnerët tanë AL</p>'],
                'en' => ['title' => 'Selected partners', 'subtitle' => 'Collaboration', 'description' => '<p>Our partners EN</p>'],
            ],
        ])->assertSessionHasNoErrors();
        foreach (['' => 'Partnerë të zgjedhur', '/en' => 'Selected partners'] as $prefix => $title) {
            foreach (['/', '/news', '/contact', '/attractions'] as $path) {
                $this->get($prefix.$path)->assertOk()->assertSee($title)
                    ->assertSeeInOrder([$second->url(), $first->url()], false)
                    ->assertDontSee('aadffinalk-logo 1.svg', false);
            }
        }
        $partners->update(['is_active' => false]);
        $this->get('/en/news')->assertOk()->assertDontSee('data-footer-partners', false)->assertDontSee('Selected partners');
        $partners->update(['is_active' => true]);
        foreach (['draft', 'published'] as $status) {
            $page->update(['status' => $status, 'published_at' => $status === 'draft' ? now()->subDay() : now()->addDay()]);
            $this->get('/en/news')->assertOk()->assertDontSee('Selected partners')->assertDontSee($second->url(), false);
        }
        $page->update(['status' => 'published', 'published_at' => now()->subDay()]);
        $partners->syncGallery([]);
        $this->get('/en/news')->assertOk()->assertSee('Selected partners')->assertSee('aadffinalk-logo 1.svg', false);
    }

    public function test_four_pillar_backgrounds_have_independent_website_content_image_controls(): void
    {
        $this->actingAs(User::factory()->create());
        Page::factory()->published()->create(['is_homepage' => true]);
        $images = [];
        foreach (['education', 'innovation', 'business', 'art'] as $pillar) {
            $media = Media::factory()->create(['path' => "media/$pillar-pillar.png"]);
            $images[sha1("template/images/$pillar.png")] = $media->id;
        }
        $this->put(route('admin.website-content.update'), ['group' => 'images', 'images' => $images])->assertSessionHasNoErrors();
        foreach (['/', '/en'] as $url) {
            $response = $this->get($url)->assertOk();
            foreach (['education', 'innovation', 'business', 'art'] as $pillar) {
                $response->assertSee("media/$pillar-pillar.png");
            }
        }
        $this->get(route('admin.website-content.edit', ['group' => 'images']))->assertSee('Homepage pillar background: Business')->assertSee('Homepage pillar background: Art');
    }

    public function test_retained_label_guidance_does_not_mark_literal_public_template_keys_as_unused(): void
    {
        $source = collect(File::allFiles(resource_path('views/public')))->map(fn ($file) => $file->getContents())->implode("\n");
        $defaults = WebsiteContent::defaults('en');
        foreach (config('content_usage.retained_texts') as $key) {
            $this->assertArrayHasKey($key, $defaults);
            $this->assertDoesNotMatchRegularExpression('/(?:__|trans)\(\s*[\'"]'.preg_quote($key, '/').'[\'"]/', $source, $key.' is used publicly; update the editor guidance.');
        }
    }

    public function test_editor_updates_homepage_sections_in_both_languages_and_fallbacks_do_not_override_them(): void
    {
        $this->actingAs(User::factory()->create());
        $page = Page::factory()->published()->create(['is_homepage' => true]);
        $discover = PageSection::factory()->for($page)->create(['internal_name' => 'Homepage - Discover', 'type' => SectionType::TextImage]);
        $pillars = PageSection::factory()->for($page)->create(['internal_name' => 'Homepage - Pillars', 'type' => SectionType::Features]);

        foreach ([$discover, $pillars] as $section) {
            $translations = [];
            foreach (['al', 'en'] as $locale) {
                $translations[$locale] = [
                    'title' => "$locale section $section->id title",
                    'subtitle' => "$locale section $section->id subtitle",
                    'description' => "<p>$locale section $section->id body</p>",
                    'primary_button_label' => "$locale explore",
                ];
            }
            $this->put(route('admin.sections.update', $section), [
                'page_id' => $page->id, 'internal_name' => $section->internal_name,
                'type' => $section->type->value, 'display_order' => $section->display_order,
                'is_active' => true, 'translations' => $translations,
                'primary_button_url' => '/events',
            ])->assertSessionHasNoErrors()->assertRedirect();
        }

        $this->put(route('admin.website-content.update'), [
            'group' => 'cms', 'texts' => [sha1('cms.pillars_title') => ['al' => 'AL fallback only', 'en' => 'EN fallback only']],
        ])->assertSessionHasNoErrors();
        $this->get(route('admin.website-content.edit'))->assertSee('Fallback only:')->assertSee('Homepage - Pillars')->assertSee('Retained legacy label:');
        $this->get(route('admin.sections.edit', $discover))->assertSee('Where this content appears')->assertSee('Subtitle is the large headline');

        foreach (['al' => '/', 'en' => '/en'] as $locale => $url) {
            $response = $this->get($url)->assertOk()->assertSee("$locale explore");
            foreach ([$discover, $pillars] as $section) {
                $response->assertSee("$locale section $section->id title")->assertSee("$locale section $section->id body");
            }
            $response->assertSee("$locale section $pillars->id subtitle")->assertDontSee(strtoupper($locale).' fallback only');
            $this->assertStringContainsString("$locale section $discover->id subtitle", preg_replace('/\s+/', ' ', strip_tags($response->getContent())));
        }

        $discover->update(['is_active' => false]);
        $pillars->update(['is_active' => false]);
        $this->get('/en')->assertSee('EN fallback only')->assertDontSee("en section $discover->id title");
        $this->assertSame('en section '.$discover->id.' title', $discover->fresh()->translation('en', false)->title);
    }

    public function test_attraction_gallery_and_visitor_information_remain_on_detail_not_carousel(): void
    {
        $attraction = Attraction::factory()->published()->create([
            'is_featured' => true, 'featured_media_id' => Media::factory(),
        ]);
        $gallery = Media::factory()->create(['path' => 'media/attraction-gallery.jpg']);
        $attraction->gallery()->attach($gallery->id);
        foreach (['al', 'en'] as $locale) {
            $attraction->translations()->where('locale', $locale)->update(['visitor_information' => "$locale verified visiting hours"]);
            $translation = $attraction->translation($locale, false);
            $prefix = $locale === 'en' ? '/en' : '';
            $this->get("$prefix/attractions")->assertOk()->assertDontSee($gallery->url(), false);
            $this->get("$prefix/attractions/$translation->slug")->assertOk()
                ->assertSee($gallery->url(), false)->assertSee("$locale verified visiting hours");
            $this->get($prefix ?: '/')->assertOk()->assertSee($attraction->featuredMedia->url(), false);
        }
        $this->actingAs(User::factory()->create())->get(route('admin.attractions.edit', $attraction))
            ->assertOk()->assertSee('Galleries are retained; no new popup is enabled.')->assertSee($gallery->original_name);
        $this->assertDatabaseHas('media_attachments', ['media_id' => $gallery->id]);
    }

    public function test_business_public_email_is_connected_and_about_unused_fields_are_explained(): void
    {
        $business = Business::factory()->published()->create(['email' => 'public@example.test']);
        foreach (['al', 'en'] as $locale) {
            $this->get(route('public.businesses.show', [$locale, $business->translation($locale, false)->slug]))
                ->assertOk()->assertSee('mailto:public@example.test', false);
        }
        $page = Page::factory()->published()->create();
        $page->translations()->where('locale', 'en')->update(['slug' => 'about-us']);
        $section = PageSection::factory()->for($page)->create(['internal_name' => 'About - Mission']);
        $this->actingAs(User::factory()->create())->get(route('admin.sections.edit', $section))->assertOk()
            ->assertSee('AL/EN JSON items')->assertSee('retained but not displayed');
    }
}
