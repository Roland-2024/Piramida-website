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
