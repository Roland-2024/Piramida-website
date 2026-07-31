<?php

namespace Tests\Feature;

use App\Enums\BusinessCategory;
use App\Enums\ContentStatus;
use App\Models\Business;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_a_bilingual_business_with_popup_media(): void
    {
        $editor = User::factory()->create();
        $logo = Media::factory()->create();
        $cover = Media::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.businesses.store'), [
                'category' => BusinessCategory::Shop->value,
                'status' => ContentStatus::Published->value,
                'published_at' => now()->subMinute()->format('Y-m-d H:i:s'),
                'website_url' => 'https://example.test/shop',
                'email' => null,
                'phone' => '+355 69 000 0000',
                'is_featured' => true,
                'display_order' => 4,
                'featured_media_id' => $cover->id,
                'logo_media_id' => $logo->id,
                'gallery_media_ids' => [$cover->id],
                'translations' => [
                    'al' => [
                        'name' => 'Dyqani Piramida',
                        'slug' => '',
                        'description' => '<p>Përshkrimi i dyqanit.</p>',
                        'address' => 'Kati 4',
                    ],
                    'en' => [
                        'name' => 'Piramida Shop',
                        'slug' => '',
                        'description' => '<p>Shop description.</p>',
                        'address' => 'Floor 4',
                    ],
                ],
            ])
            ->assertRedirect();

        $business = Business::query()->firstOrFail();

        $this->assertSame('dyqani-piramida', $business->translation('al', false)?->slug);
        $this->assertSame('piramida-shop', $business->translation('en', false)?->slug);
        $this->assertSame($logo->id, $business->logo_media_id);
        $this->assertSame([$cover->id], $business->gallery()->pluck('media.id')->all());
        $this->assertSame($editor->id, $business->created_by);
    }

    public function test_editor_cannot_delete_business_but_admin_can_restore_it(): void
    {
        $business = Business::factory()->create();
        $editor = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($editor)
            ->delete(route('admin.businesses.destroy', $business))
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.businesses.destroy', $business))
            ->assertRedirect(route('admin.businesses.index'));

        $this->actingAs($admin)
            ->post(route('admin.businesses.restore', ['id' => $business->id]))
            ->assertRedirect(route('admin.businesses.index'));

        $this->assertNotSoftDeleted($business);
    }

    public function test_public_business_cards_hide_drafts_and_include_information_dialogs(): void
    {
        $published = Business::factory()->published()->create();
        $draft = Business::factory()->create();

        $this->get(route('public.businesses.index', 'en'))
            ->assertOk()
            ->assertSee("Business {$published->id}")
            ->assertSee("business-{$published->id}", false)
            ->assertSee('Business description.')
            ->assertDontSee("Business {$draft->id}");

        $this->get(route('public.businesses.show', ['en', "business-{$published->id}"]))
            ->assertOk();

        $this->get(route('public.businesses.show', ['en', "biznesi-{$published->id}"]))
            ->assertNotFound();
    }
}
