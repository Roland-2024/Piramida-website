<?php

namespace Tests\Feature;

use App\Enums\SectionType;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSectionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_a_bilingual_section_with_structured_data(): void
    {
        $page = Page::factory()->create();
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.sections.store'), [
                'page_id' => $page->id,
                'internal_name' => 'Homepage hero',
                'type' => SectionType::Hero->value,
                'display_order' => 2,
                'is_active' => true,
                'structured_data' => '{"theme":"dark"}',
                'primary_button_url' => '/al/news',
                'secondary_button_url' => null,
                'primary_media_id' => null,
                'secondary_media_id' => null,
                'translations' => [
                    'al' => [
                        'title' => 'Mirë se vini',
                        'subtitle' => null,
                        'description' => '<p>Përshkrimi</p>',
                        'primary_button_label' => 'Lajme',
                        'secondary_button_label' => null,
                    ],
                    'en' => [
                        'title' => 'Welcome',
                        'subtitle' => null,
                        'description' => '<p>Description</p>',
                        'primary_button_label' => 'News',
                        'secondary_button_label' => null,
                    ],
                ],
            ])
            ->assertRedirect();

        $section = PageSection::query()->firstOrFail();

        $this->assertSame(['theme' => 'dark'], $section->structured_data);
        $this->assertSame('Welcome', $section->translation('en', false)?->title);
    }

    public function test_page_sections_are_returned_in_deterministic_display_order(): void
    {
        $page = Page::factory()->create();
        $last = PageSection::factory()->for($page)->create(['display_order' => 20]);
        $first = PageSection::factory()->for($page)->create(['display_order' => 5]);

        $this->assertSame(
            [$first->id, $last->id],
            $page->sections()->pluck('id')->all(),
        );
    }

    public function test_section_button_urls_reject_unsafe_schemes(): void
    {
        $page = Page::factory()->create();
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.sections.store'), [
                'page_id' => $page->id,
                'internal_name' => 'Unsafe link',
                'type' => SectionType::Hero->value,
                'display_order' => 1,
                'is_active' => true,
                'structured_data' => null,
                'primary_button_url' => 'javascript:alert(1)',
                'secondary_button_url' => '//unsafe.example',
                'primary_media_id' => null,
                'secondary_media_id' => null,
                'translations' => [
                    'al' => [
                        'title' => 'Titull',
                        'subtitle' => null,
                        'description' => null,
                        'primary_button_label' => 'Kliko',
                        'secondary_button_label' => null,
                    ],
                    'en' => [
                        'title' => 'Title',
                        'subtitle' => null,
                        'description' => null,
                        'primary_button_label' => 'Click',
                        'secondary_button_label' => null,
                    ],
                ],
            ])
            ->assertSessionHasErrors(['primary_button_url', 'secondary_button_url']);
    }

    public function test_editor_can_manage_a_section_video_and_ordered_gallery(): void
    {
        $page = Page::factory()->create();
        $editor = User::factory()->create();
        $first = Media::factory()->create();
        $second = Media::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.sections.store'), [
                'page_id' => $page->id,
                'internal_name' => 'About video and gallery',
                'type' => SectionType::Gallery->value,
                'display_order' => 3,
                'is_active' => true,
                'video_url' => 'https://video.example.test/about.mp4',
                'gallery_media_ids' => [$second->id, $first->id],
                'translations' => [
                    'al' => ['title' => 'Historia'],
                    'en' => ['title' => 'History'],
                ],
            ])
            ->assertRedirect();

        $section = PageSection::query()->where('internal_name', 'About video and gallery')->firstOrFail();
        $this->assertSame('https://video.example.test/about.mp4', $section->video_url);
        $this->assertSame([$second->id, $first->id], $section->gallery()->pluck('media.id')->all());
    }
}
