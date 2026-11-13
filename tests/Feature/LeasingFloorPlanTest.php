<?php

namespace Tests\Feature;

use App\Models\LeasingUnit;
use App\Models\Media;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeasingFloorPlanTest extends TestCase
{
    use RefreshDatabase;

    private function space(): Space
    {
        return Space::factory()->published()->create(['type' => 'leasing', 'is_available' => true,
            'leasing_unit_id' => LeasingUnit::where('code', 'A1')->value('id'), 'area_sqm' => 71.78]);
    }

    private function data(Space $space): array
    {
        return ['status' => 'published', 'published_at' => now()->subDay()->toDateTimeString(),
            'display_order' => 0, 'currency' => 'ALL', 'booking_mode' => 'internal',
            'area_sqm' => 71.78, 'is_available' => '1', 'is_featured' => '0',
            'leasing_unit_id' => $space->leasing_unit_id,
            'translations' => ['al' => ['title' => 'A1'], 'en' => ['title' => 'A1']]];
    }

    public function test_editor_can_upload_attach_replace_and_detach_a_pdf_without_deleting_shared_media(): void
    {
        Storage::fake('public');
        $space = $this->space();
        $editor = User::factory()->create();
        $this->actingAs($editor)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->create('A1.pdf', 5000, 'application/pdf'),
        ])->assertSessionHasNoErrors();
        $pdf = Media::firstOrFail();
        $this->get(route('admin.leasing.edit', $space))->assertOk()->assertSee('Floor plan (PDF)')->assertSee('A1.pdf');
        $this->put(route('admin.leasing.update', $space), [...$this->data($space), 'floor_plan_media_id' => $pdf->id])
            ->assertSessionHasNoErrors();
        $this->assertSame($pdf->id, $space->fresh()->floor_plan_media_id);
        $this->assertTrue($pdf->isReferenced());
        $other = Media::factory()->create(['mime_type' => 'application/pdf', 'extension' => 'pdf']);
        $this->put(route('admin.leasing.update', $space), [...$this->data($space), 'floor_plan_media_id' => $other->id])
            ->assertSessionHasNoErrors();
        $this->assertSame($other->id, $space->fresh()->floor_plan_media_id);
        $this->assertFalse($pdf->isReferenced());
        $space->delete();
        $this->assertTrue($other->isReferenced());
        $space->restore();
        $this->put(route('admin.leasing.update', $space), [...$this->data($space), 'floor_plan_media_id' => null])
            ->assertSessionHasNoErrors();
        $this->assertNull($space->fresh()->floor_plan_media_id);
        Storage::disk('public')->assertExists($pdf->path);
    }

    public function test_only_public_non_deleted_pdfs_are_assignable_and_guests_cannot_edit(): void
    {
        $space = $this->space();
        $this->put(route('admin.leasing.update', $space), $this->data($space))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create());
        $image = Media::factory()->create();
        $private = Media::factory()->create(['disk' => 'local', 'mime_type' => 'application/pdf']);
        $deleted = Media::factory()->create(['mime_type' => 'application/pdf']);
        $deleted->delete();
        foreach ([$image->id, $private->id, $deleted->id, 999999] as $id) {
            $this->put(route('admin.leasing.update', $space), [...$this->data($space), 'floor_plan_media_id' => $id])
                ->assertSessionHasErrors('floor_plan_media_id');
        }
        $this->post(route('admin.media.store'), ['file' => UploadedFile::fake()->create('fake.pdf', 10, 'application/x-php')])
            ->assertSessionHasErrors('file');
        $this->post(route('admin.media.store'), ['file' => UploadedFile::fake()->create('huge.pdf', 10241, 'application/pdf')])
            ->assertSessionHasErrors('file');
    }

    public function test_download_and_button_require_a_published_available_localized_leasing_space_and_pdf(): void
    {
        Storage::fake('public');
        $space = $this->space();
        $pdf = Media::factory()->create(['mime_type' => 'application/pdf', 'extension' => 'pdf']);
        $url = route('public.spaces.floor-plan', ['en', $space->translation('en')->slug]);
        $page = route('public.spaces.show', ['en', $space->translation('en')->slug]);
        $this->get($page)->assertOk()->assertDontSee('class="leasing-form-plan"', false);
        $this->get($url)->assertNotFound();
        $space->update(['floor_plan_media_id' => $pdf->id]);
        Storage::disk('public')->put($pdf->path, '%PDF-1.4 test');
        foreach (['al', 'en'] as $locale) {
            $this->get(route('public.spaces.show', [$locale, $space->translation($locale)->slug]))
                ->assertOk()->assertSee('class="leasing-form-plan"', false);
            $this->get(route('public.spaces.floor-plan', [$locale, $space->translation($locale)->slug]))
                ->assertOk()->assertDownload('floor-plan-'.$space->id.'.pdf')->assertHeader('Content-Type', 'application/pdf');
        }
        foreach ([['status' => 'draft'], ['status' => 'published', 'published_at' => now()->addDay()],
            ['published_at' => now()->subDay(), 'is_available' => false], ['is_available' => true, 'type' => 'event_space']] as $changes) {
            $space->update($changes);
            $this->get($url)->assertNotFound();
        }
        $space->update(['type' => 'leasing']);
        $space->translations()->where('locale', 'en')->delete();
        $this->get($url)->assertNotFound();
        $space->delete();
        $this->get(route('public.spaces.floor-plan', ['al', $space->translation('al')->slug]))->assertNotFound();
    }

    public function test_business_category_is_required_allowlisted_and_localized(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        $space = $this->space();
        foreach (['al' => 'Kategoria e Biznesit', 'en' => 'Business Category'] as $locale => $label) {
            $this->get(route('public.spaces.show', [$locale, $space->translation($locale)->slug]))->assertOk()->assertSee($label);
        }
        $url = route('public.spaces.leasing-request', ['en', $space->translation('en')->slug]);
        foreach ([null, 'other', ['shop']] as $invalid) {
            $this->post($url, ['business_category' => $invalid])->assertSessionHasErrors('business_category');
        }
        foreach (config('leasing.business_categories') as $category) {
            $this->post($url, ['business_category' => $category])->assertSessionDoesntHaveErrors('business_category');
        }
        $this->assertDatabaseCount('submissions', 0);
    }
}
