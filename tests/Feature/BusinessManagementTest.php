<?php

namespace Tests\Feature;

use App\Enums\BusinessCategory;
use App\Enums\ContentStatus;
use App\Models\Business;
use App\Models\Media;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
                'category_slugs' => [BusinessCategory::Education->value, BusinessCategory::SocialSpaces->value],
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
        $this->assertSame(['education', 'social_spaces'], $business->category_slugs);
        $this->get(route('admin.businesses.edit', $business))->assertOk()
            ->assertSee('name="category_slugs[]"', false)->assertSee('Social Spaces')
            ->assertDontSee('value="cafe"', false);
    }

    public function test_editor_can_replace_categories_but_cannot_submit_empty_unknown_or_duplicate_categories(): void
    {
        $this->actingAs(User::factory()->create());
        $business = Business::factory()->published()->create();
        $payload = [
            'category_slugs' => ['innovation', 'art_culture'],
            'status' => 'published', 'published_at' => now()->subDay()->toDateTimeString(),
            'is_featured' => false, 'display_order' => 0,
            'translations' => [
                'al' => ['name' => 'Test biznes', 'slug' => 'test-biznes'],
                'en' => ['name' => 'Test business', 'slug' => 'test-business'],
            ],
        ];
        $url = route('admin.businesses.update', $business);
        $this->put($url, $payload)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(['innovation', 'art_culture'], $business->fresh()->category_slugs);
        foreach ([[], ['cafe'], ['custom_category'], ['education', 'education']] as $invalid) {
            $this->put($url, [...$payload, 'category_slugs' => $invalid])->assertSessionHasErrors();
            $this->assertSame(['innovation', 'art_culture'], $business->fresh()->category_slugs);
        }
    }

    public function test_directory_only_lists_social_spaces_while_business_carousel_keeps_all_categories(): void
    {
        $social = Business::factory()->published()->create();
        $social->categories()->sync(['social_spaces', 'education']);
        $innovation = Business::factory()->published()->create();
        $innovation->categories()->sync(['innovation']);
        $future = Business::factory()->published()->create(['published_at' => now()->addDay()]);
        $page = Page::factory()->published()->create();
        $page->translations()->where('locale', 'en')->update(['slug' => 'business']);
        foreach (['al', 'en'] as $locale) {
            $this->get(route('public.businesses.index', $locale))->assertOk()
                ->assertViewHas('items', fn ($items) => $items->modelKeys() === [$social->id]);
            $this->get(route('public.pages.show', [$locale, $page->translation($locale, false)->slug]))->assertOk()
                ->assertViewHas('slides', fn ($slides) => $slides->count() === 2);
        }
        $social->translations()->where('locale', 'en')->delete();
        $this->get('/en/businesses')->assertOk()->assertViewHas('items', fn ($items) => $items->isEmpty());
        $this->assertNotNull($future->fresh());
    }

    public function test_category_migration_preserves_existing_businesses_and_maps_legacy_values(): void
    {
        $migration = require database_path('migrations/2026_10_09_000001_add_business_categories.php');
        $migration->down();
        $ids = [];
        foreach (['cafe' => 'social_spaces', 'restaurant' => 'social_spaces', 'shop' => 'social_spaces', 'technology' => 'innovation', 'art' => 'art_culture'] as $old => $new) {
            $id = DB::table('businesses')->insertGetId(['category' => $old, 'deleted_at' => now()]);
            $ids[$id] = [$old, $new];
        }
        $migration->up();
        $this->assertDatabaseCount('business_categories', 5);
        foreach ($ids as $id => [$old, $new]) {
            $this->assertDatabaseHas('businesses', ['id' => $id, 'legacy_category' => $old]);
            $this->assertDatabaseHas('business_category', ['business_id' => $id, 'category_slug' => $new]);
        }
        $migration->down();
        foreach ($ids as $id => [$old]) {
            $this->assertDatabaseHas('businesses', ['id' => $id, 'category' => $old]);
        }
        $migration->up();
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
            ->assertDontSee('class="template-pagination"', false)
            ->assertDontSee("Business {$draft->id}");

        $this->get(route('public.businesses.show', ['en', "business-{$published->id}"]))
            ->assertOk();

        $this->get(route('public.businesses.show', ['en', "biznesi-{$published->id}"]))
            ->assertNotFound();

        Business::factory()->published()->count(12)->create();
        $this->get(route('public.businesses.index', 'en'))->assertOk()
            ->assertSee('class="template-pagination" style="background:#000"', false);
    }
}
