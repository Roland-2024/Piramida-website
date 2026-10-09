<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\SpaceType;
use App\Models\LeasingUnit;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeasingMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_real_inventory_and_all_floor_templates_render_in_both_languages(): void
    {
        $this->assertDatabaseCount('leasing_units', 52);
        $this->assertDatabaseMissing('leasing_units', ['code' => 'A16']);
        $this->assertDatabaseHas('leasing_units', ['code' => 'BE1/1', 'svg_id' => 'unit-BE1-1']);
        foreach (['al', 'en'] as $locale) {
            $prefix = $locale === 'en' ? '/en' : '';
            $this->get("$prefix/spaces?type=leasing")->assertRedirect("$prefix/leasing");
            $this->get("$prefix/leasing")->assertOk()
                ->assertSee('template/images/leasing/piramida-final.webp')
                ->assertSee('piramida-map-lines--desktop')
                ->assertSee('piramida-map-lines--mobile')
                ->assertDontSee('Piramida_map.png')
                ->assertSee('data-floor="minus-one"', false);
            foreach (LeasingUnit::FLOORS as $floor => $info) {
                $response = $this->get("$prefix/leasing/floors/$floor")->assertOk();
                $response->assertSee(__('cms.unit_unavailable_notice', [], $locale))
                    ->assertSee('aria-describedby="unit-tooltip"', false);
                $response->assertSee($info[$locale])->assertSee('template/images/leasing/');
                foreach (LeasingUnit::where('floor', $floor)->get() as $unit) {
                    $response->assertSee('id="'.$unit->svg_id.'"', false);
                }
                $response->assertDontSee('data-status="available"', false);
            }
        }
        $this->get('/en/leasing/floors/unknown')->assertNotFound();
    }

    public function test_only_available_published_translated_units_link_to_the_existing_form(): void
    {
        $unit = LeasingUnit::where('code', 'A1')->firstOrFail();
        $space = Space::factory()->published()->create([
            'type' => SpaceType::Leasing, 'leasing_unit_id' => $unit->id, 'is_available' => true,
        ]);
        $url = route('public.spaces.show', ['en', "space-$space->id"]);
        $this->get('/en/leasing/floors/ground')->assertOk()
            ->assertSee('data-status="available"', false)->assertSee('href="'.$url.'"', false);
        $this->get($url)->assertOk()->assertSee('data-leasing-form', false);
        $this->get($url)->assertSee('Ground Floor — A1');

        foreach ([
            ['is_available' => false],
            ['is_available' => true, 'status' => ContentStatus::Draft],
            ['status' => ContentStatus::Published, 'published_at' => now()->addDay()],
            ['published_at' => now()->subDay(), 'leasing_unit_id' => null],
        ] as $state) {
            $space->update($state);
            $this->get('/en/leasing/floors/ground')->assertOk()
                ->assertDontSee('data-status="available"', false)->assertDontSee($url, false);
            $this->get($url)->assertNotFound();
            $this->post(route('public.spaces.leasing-request', ['en', "space-$space->id"]), [])->assertNotFound();
        }
        $space->update(['leasing_unit_id' => $unit->id]);
        $space->translations()->where('locale', 'en')->delete();
        $this->get('/en/leasing/floors/ground')->assertDontSee($url, false);
        $this->get($url)->assertNotFound();
        $this->get('/leasing/floors/ground')->assertSee('data-status="available"', false);
        $space->delete();
        $this->get('/leasing/floors/ground')->assertDontSee('data-status="available"', false);
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_admin_modules_are_isolated_and_unit_assignment_is_validated(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $unit = LeasingUnit::where('code', 'D1')->firstOrFail();
        $this->post(route('admin.leasing.store'), $this->payload([
            'type' => 'event_space', 'leasing_unit_id' => $unit->id, 'is_available' => '1',
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $lease = Space::firstOrFail();
        $this->assertSame(SpaceType::Leasing, $lease->type);
        $this->assertTrue($lease->is_available);
        $this->get(route('admin.leasing.index'))->assertSee('Test leasing');
        $this->get(route('admin.spaces.index'))->assertDontSee('Test leasing');
        $this->get(route('admin.leasing.edit', $lease))->assertOk()->assertSee('3rd Floor');
        $this->get(route('admin.spaces.edit', $lease))->assertNotFound();
        $this->put(route('admin.spaces.update', $lease), $this->payload())->assertNotFound();
        $this->delete(route('admin.spaces.destroy', $lease))->assertNotFound();

        $this->post(route('admin.leasing.store'), $this->payload([
            'leasing_unit_id' => $unit->id,
        ]))->assertSessionHasErrors('leasing_unit_id');
        $this->post(route('admin.leasing.store'), $this->payload([
            'leasing_unit_id' => 99999,
        ]))->assertSessionHasErrors('leasing_unit_id');
        $this->post(route('admin.leasing.store'), $this->payload([
            'is_available' => '1',
        ]))->assertSessionHasErrors('leasing_unit_id');
        $this->post(route('admin.leasing.store'), $this->payload([
            'is_available' => '1', 'area_sqm' => 0,
        ]))->assertSessionHasErrors('area_sqm');
        $this->put(route('admin.leasing.update', $lease), $this->payload([
            'leasing_unit_id' => $unit->id, 'is_available' => '0',
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertFalse($lease->fresh()->is_available);
        $this->delete(route('admin.leasing.destroy', $lease))->assertRedirect();
        $this->post(route('admin.spaces.restore', $lease->id))->assertNotFound();
        $this->post(route('admin.leasing.restore', $lease->id))->assertRedirect();

        $event = Space::factory()->create(['type' => SpaceType::EventSpace]);
        $this->get(route('admin.leasing.edit', $event))->assertNotFound();
        $this->actingAs(User::factory()->create(['is_active' => false]))
            ->get(route('admin.leasing.index'))->assertRedirect();
    }

    public function test_editor_can_assign_a_basement_unit_and_only_publish_available_translated_leases(): void
    {
        $this->actingAs(User::factory()->create());
        $unit = LeasingUnit::where('code', 'U12')->firstOrFail();
        $this->get(route('admin.leasing.create'))->assertOk()->assertSee('-1 Floor');
        $this->post(route('admin.leasing.store'), $this->payload([
            'leasing_unit_id' => $unit->id, 'is_available' => '1',
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $space = Space::firstOrFail();
        $url = route('public.spaces.show', ['en', 'test-leasing']);
        $this->get('/en/leasing/floors/minus-one')->assertOk()
            ->assertSee('kati-1.webp')->assertSee('href="'.$url.'"', false);
        $this->get($url)->assertOk()->assertSee('-1 Floor')->assertSee('U12');
        $space->update(['is_available' => false]);
        $this->get('/en/leasing/floors/minus-one')->assertOk()->assertDontSee('data-status="available"', false);
        $this->get($url)->assertNotFound();
        $space->update(['is_available' => true]);
        $space->translations()->where('locale', 'en')->delete();
        $this->get('/en/leasing/floors/minus-one')->assertDontSee('data-status="available"', false);
        $this->get('/leasing/floors/minus-one')->assertSee('data-status="available"', false);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'status' => 'published', 'published_at' => now()->subDay()->toDateTimeString(),
            'display_order' => 0, 'currency' => 'EUR', 'booking_mode' => 'internal',
            'area_sqm' => 100, 'is_featured' => '0',
            'translations' => [
                'al' => ['title' => 'Test leasing', 'slug' => 'test-qira'],
                'en' => ['title' => 'Test leasing', 'slug' => 'test-leasing'],
            ],
        ], $overrides);
    }
}
