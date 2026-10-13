<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\SpaceType;
use App\Models\LeasingUnit;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LeasingMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_real_inventory_and_all_floor_templates_render_in_both_languages(): void
    {
        $this->assertDatabaseCount('leasing_units', 53);
        $this->assertDatabaseHas('leasing_units', ['floor' => 'roof', 'code' => 'D2', 'svg_id' => 'unit-D2']);
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

    public function test_roof_d2_migration_preserves_existing_units_and_assignments(): void
    {
        DB::table('leasing_units')->where('code', 'D2')->delete();
        $originalUnits = DB::table('leasing_units')->orderBy('id')->get();
        $e1 = LeasingUnit::where('code', 'E1')->firstOrFail();
        $space = Space::factory()->published()->create([
            'type' => SpaceType::Leasing, 'leasing_unit_id' => $e1->id, 'is_available' => true,
        ]);

        $migration = require database_path('migrations/2026_10_13_000001_add_roof_d2_leasing_unit.php');
        $migration->up();

        $this->assertEquals($originalUnits, DB::table('leasing_units')->where('code', '!=', 'D2')->orderBy('id')->get());
        $this->assertSame($e1->id, $space->fresh()->leasing_unit_id);
        $this->assertDatabaseCount('leasing_units', 53);
        $this->assertDatabaseHas('leasing_units', ['floor' => 'roof', 'code' => 'D2', 'svg_id' => 'unit-D2']);
    }

    public function test_editor_can_assign_roof_d2_and_publication_rules_control_its_map_link(): void
    {
        $unit = LeasingUnit::where('code', 'D2')->firstOrFail();
        $unavailable = 'id="unit-D2" class="floor-plan-unit" data-unit="D2" data-status="unavailable"';
        $available = 'id="unit-D2" class="floor-plan-unit" data-unit="D2" data-status="available"';
        $shape = 'M837 280.5V326L964 340.5L977.5 269.5V229L855 210.5L837 280.5Z';
        foreach (['/leasing/floors/roof', '/en/leasing/floors/roof'] as $url) {
            $this->get($url)->assertOk()->assertSee($unavailable, false)->assertSee($shape, false);
        }
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.leasing.create'))->assertOk()->assertSee('Roof L+4 — D2');
        $this->post(route('admin.leasing.store'), $this->payload([
            'leasing_unit_id' => $unit->id, 'is_available' => '1',
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $space = Space::firstOrFail();
        $this->assertSame('roof', $space->leasingUnit->floor);
        $this->assertSame($unit->id, $space->leasing_unit_id);
        $this->post(route('admin.leasing.store'), $this->payload([
            'leasing_unit_id' => $unit->id,
        ]))->assertSessionHasErrors('leasing_unit_id');
        foreach (['al' => 'test-qira', 'en' => 'test-leasing'] as $locale => $slug) {
            $url = route('public.spaces.show', [$locale, $slug]);
            $this->get(route('public.leasing.floor', [$locale, 'roof']))->assertOk()
                ->assertSee($available, false)->assertSee('href="'.$url.'"', false);
            $this->get($url)->assertOk()->assertSee('D2');
        }
        foreach ([
            ['is_available' => false],
            ['is_available' => true, 'status' => ContentStatus::Draft],
            ['status' => ContentStatus::Published, 'published_at' => now()->addDay()],
        ] as $state) {
            $space->update($state);
            $this->get('/en/leasing/floors/roof')->assertSee($unavailable, false);
            $this->get('/en/spaces/test-leasing')->assertNotFound();
        }
        $space->update(['published_at' => now()->subDay()]);
        $space->translations()->where('locale', 'en')->delete();
        $this->get('/en/leasing/floors/roof')->assertSee($unavailable, false);
        $this->get('/leasing/floors/roof')->assertSee($available, false);
        $this->assertDatabaseCount('submissions', 0);
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
