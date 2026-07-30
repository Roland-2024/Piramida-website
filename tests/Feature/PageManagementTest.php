<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_a_bilingual_page_with_generated_slugs(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.pages.store'), $this->validPageData())
            ->assertRedirect();

        $page = Page::query()->firstOrFail();

        $this->assertSame('rreth-nesh', $page->translation('al', false)?->slug);
        $this->assertSame('about-us', $page->translation('en', false)?->slug);
        $this->assertSame($editor->id, $page->created_by);
    }

    public function test_translated_slug_must_be_unique_within_its_locale(): void
    {
        Page::factory()->create();
        $editor = User::factory()->create();
        $data = $this->validPageData();
        $data['translations']['al']['slug'] = 'faqja-1';

        $this->actingAs($editor)
            ->post(route('admin.pages.store'), $data)
            ->assertSessionHasErrors('translations.al.slug');
    }

    public function test_editor_cannot_delete_content_but_admin_can_restore_it(): void
    {
        $page = Page::factory()->create();
        $editor = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($editor)
            ->delete(route('admin.pages.destroy', $page))
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.pages.destroy', $page))
            ->assertRedirect(route('admin.pages.index'));

        $this->assertSoftDeleted($page);

        $this->actingAs($admin)
            ->post(route('admin.pages.restore', $page->id))
            ->assertRedirect(route('admin.pages.index'));

        $this->assertNotSoftDeleted($page);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPageData(): array
    {
        return [
            'status' => ContentStatus::Published->value,
            'published_at' => now()->subMinute()->format('Y-m-d H:i:s'),
            'display_order' => 1,
            'featured_media_id' => null,
            'is_homepage' => false,
            'translations' => [
                'al' => [
                    'title' => 'Rreth nesh',
                    'slug' => '',
                    'short_description' => 'Përshkrimi',
                    'content' => '<p>Përmbajtja</p>',
                    'seo_title' => 'Rreth nesh',
                    'seo_description' => 'SEO',
                ],
                'en' => [
                    'title' => 'About us',
                    'slug' => '',
                    'short_description' => 'Description',
                    'content' => '<p>Content</p>',
                    'seo_title' => 'About us',
                    'seo_description' => 'SEO',
                ],
            ],
        ];
    }
}
