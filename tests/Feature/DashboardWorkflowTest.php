<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\Business;
use App\Models\Career;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Program;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_groups_open_for_the_current_page_and_preserve_role_visibility(): void
    {
        foreach ([false, true] as $admin) {
            $user = $admin ? User::factory()->admin()->create() : User::factory()->create();
            $response = $this->actingAs($user)->get(route('admin.news.index'))->assertOk();
            $document = new \DOMDocument;
            @$document->loadHTML($response->getContent());
            $xpath = new \DOMXPath($document);
            $this->assertSame($admin ? 4 : 3, $xpath->query('//details[@name="admin-menu"]')->length);
            $open = $xpath->query('//details[@name="admin-menu" and @open]/summary');
            $this->assertSame(1, $open->length);
            $this->assertSame('Activities & careers', trim($open->item(0)->textContent));
            $this->assertSame(1, $xpath->query('//nav//a[@aria-current="page" and normalize-space(.)="News"]')->length);
            $this->assertSame($admin ? 1 : 0, $xpath->query('//nav//a[normalize-space(.)="Users"]')->length);
            $this->assertSame($admin ? 1 : 0, $xpath->query('//nav//a[normalize-space(.)="Site settings"]')->length);
            $this->assertSame($admin ? 1 : 0, $xpath->query('//nav//a[normalize-space(.)="Submissions"]')->length);
        }
    }

    public function test_template_identifiers_are_locked_but_content_and_generic_slugs_remain_editable(): void
    {
        $this->actingAs(User::factory()->create());
        $page = Page::factory()->create();
        foreach (['about-us', ...Page::CAROUSEL_SLUGS] as $slug) {
            $page->translations()->where('locale', 'en')->update(['slug' => $slug]);
            $data = ['status' => 'draft', 'is_homepage' => false, 'display_order' => 0,
                'translations' => ['al' => ['title' => 'Titulli', 'slug' => 'titulli'], 'en' => ['title' => 'Changed title', 'slug' => 'changed']]];
            $this->put(route('admin.pages.update', $page), $data)->assertSessionHasErrors('translations.en.slug');
            $data['translations']['en']['slug'] = $slug;
            $this->put(route('admin.pages.update', $page), $data)->assertSessionHasNoErrors();
            $this->get(route('admin.pages.edit', $page))->assertOk()->assertSee('Locked template identifier');
        }
        $generic = Page::factory()->create();
        $data['translations']['al']['slug'] = 'generic-al';
        $data['translations']['en']['slug'] = 'generic-en';
        $this->put(route('admin.pages.update', $generic), $data)->assertSessionHasNoErrors();

        $section = PageSection::factory()->for($generic)->create(['internal_name' => 'About - History']);
        $sectionData = ['page_id' => $generic->id, 'internal_name' => 'Changed', 'type' => 'custom',
            'display_order' => 0, 'is_active' => true, 'translations' => ['al' => ['title' => 'Historia'], 'en' => ['title' => 'History']]];
        $this->put(route('admin.sections.update', $section), $sectionData)->assertSessionHasErrors('internal_name');
        $sectionData['internal_name'] = 'About - History';
        $this->put(route('admin.sections.update', $section), $sectionData)->assertSessionHasNoErrors();
        $sectionData['page_id'] = Page::factory()->create()->id;
        $this->put(route('admin.sections.update', $section), $sectionData)->assertSessionHasErrors('page_id');
    }

    public function test_carousels_no_longer_use_or_expose_legacy_sections(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        foreach (Page::CAROUSEL_SLUGS as $slug) {
            $page = Page::factory()->published()->create();
            $page->translations()->where('locale', 'en')->update(['slug' => $slug]);
            $section = PageSection::factory()->for($page)->create(['internal_name' => 'Legacy-'.$slug, 'primary_media_id' => Media::factory()]);
            $url = $slug === 'business' ? route('admin.businesses.index')
                : route('admin.programs.index', ['category' => $slug === 'art' ? 'art_culture' : $slug]);
            $this->get(route('admin.sections.edit', $section))->assertRedirect($url);
            $this->get(route('admin.sections.show', $section))->assertRedirect($url);
            $this->get(route('admin.sections.create', ['page_id' => $page->id]))->assertRedirect($url);
            $this->get(route('admin.sections.index'))->assertOk()->assertDontSee('Legacy-'.$slug);
            $this->get(route('admin.pages.show', $page))->assertOk()->assertSee('dynamic', false)->assertDontSee('Add section');
            $this->put(route('admin.sections.update', $section), [])->assertForbidden();
            $this->delete(route('admin.sections.destroy', $section))->assertForbidden();
            $this->post(route('admin.sections.store'), ['page_id' => $page->id])->assertSessionHasErrors('page_id');
            $this->get('/en/'.$slug)->assertOk()->assertViewHas('slides', fn ($slides) => $slides->isEmpty());
            $this->assertNotSoftDeleted($section);
        }
    }

    public function test_carousel_form_preserves_unused_data_and_filters_by_page(): void
    {
        $this->actingAs(User::factory()->create());
        $post = Program::factory()->create(['category' => 'education', 'booking_mode' => 'external',
            'external_url' => 'https://example.test/book', 'starts_at' => now(), 'is_featured' => true]);
        $image = Media::factory()->create();
        $post->gallery()->attach($image);
        $post->translations()->update(['seo_title' => 'Retained SEO', 'location' => 'Retained location']);
        $this->get(route('admin.programs.edit', $post))->assertOk()->assertSee('Carousel caption')
            ->assertDontSee('name="booking_mode"', false)->assertDontSee('name="gallery_media_ids[]"', false)
            ->assertDontSee('SEO title')->assertDontSee('&lt;p&gt;', false);
        $data = ['category' => 'education', 'status' => 'draft', 'display_order' => 3,
            'booking_mode' => 'none', 'gallery_media_ids' => [],
            'translations' => ['al' => ['title' => 'New AL', 'description' => 'Caption AL', 'seo_title' => null],
                'en' => ['title' => 'New EN', 'description' => 'Caption EN', 'seo_title' => null]]];
        $this->put(route('admin.programs.update', $post), $data)->assertSessionHasNoErrors();
        $post->refresh();
        $this->assertSame('external', $post->booking_mode->value);
        $this->assertSame('https://example.test/book', $post->external_url);
        $this->assertNotNull($post->starts_at);
        $this->assertTrue($post->is_featured);
        $this->assertSame([$image->id], $post->gallery->modelKeys());
        $this->assertSame('Retained SEO', $post->translation('al')->seo_title);
        $this->assertSame('Retained location', $post->translation('en')->location);
        Program::factory()->create(['category' => 'innovation']);
        Program::factory()->create(['category' => 'art_culture']);
        $this->get(route('admin.programs.index', ['category' => 'education']))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->count() === 1 && $items->first()->id === $post->id);
        $this->get(route('admin.programs.index', ['category' => 'invalid']))->assertSessionHasErrors('category');
    }

    public function test_all_image_fields_reject_documents_private_and_deleted_media(): void
    {
        $this->actingAs(User::factory()->create());
        $image = Media::factory()->create();
        $pdf = Media::factory()->create(['mime_type' => 'application/pdf', 'original_name' => 'not-an-image.pdf']);
        $private = Media::factory()->create(['disk' => 'local', 'original_name' => 'private-image.jpg']);
        $deleted = Media::factory()->create();
        $deleted->delete();
        foreach (['pages' => ['featured_media_id'], 'news' => ['featured_media_id'], 'events' => ['featured_media_id'],
            'businesses' => ['featured_media_id', 'logo_media_id'], 'programs' => ['featured_media_id'],
            'sections' => ['primary_media_id', 'secondary_media_id']] as $module => $fields) {
            foreach ([$pdf, $private, $deleted] as $invalid) {
                $this->post(route("admin.{$module}.store"), array_fill_keys($fields, $invalid->id))->assertSessionHasErrors($fields);
            }
            $this->post(route("admin.{$module}.store"), array_fill_keys($fields, $image->id))->assertSessionDoesntHaveErrors($fields);
            $this->get(route("admin.{$module}.create"))->assertOk()->assertSee('data-image-preview', false)
                ->assertDontSee('not-an-image.pdf')->assertDontSee('private-image.jpg');
        }
    }

    public function test_dashboard_counts_programs_and_links_recent_content_from_each_catalogue(): void
    {
        $this->actingAs(User::factory()->create());
        $program = Program::factory()->published()->create(['category' => 'education']);
        Program::factory()->create(['category' => 'innovation']);
        $attraction = Attraction::factory()->create();
        $business = Business::factory()->create();
        $career = Career::factory()->create();
        $space = Space::factory()->create(['type' => 'event_space']);
        $leasing = Space::factory()->create(['type' => 'leasing']);
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('metrics', fn ($metrics) => $metrics['programs'] === 2 && $metrics['published'] === 1 && $metrics['drafts'] === 6)
            ->assertSee(route('admin.programs.edit', $program), false)
            ->assertSee(route('admin.attractions.edit', $attraction), false)
            ->assertSee(route('admin.businesses.edit', $business), false)
            ->assertSee(route('admin.careers.edit', $career), false)
            ->assertSee(route('admin.spaces.edit', $space), false)
            ->assertSee(route('admin.leasing.edit', $leasing), false);
    }
}
