<?php

namespace Tests\Feature;

use App\Models\LeasingUnit;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class LeasingPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_existing_matched_leasing_posts_receive_supplied_prices_on_explicit_apply(): void
    {
        $unit = LeasingUnit::where('code', 'A4')->firstOrFail();
        $space = Space::factory()->create(['type' => 'leasing', 'leasing_unit_id' => $unit->id,
            'price_from' => 22, 'currency' => 'EUR', 'area_sqm' => 110.2]);
        $other = Space::factory()->create(['type' => 'event_space', 'price_from' => 99, 'currency' => 'EUR']);
        $unknown = Space::factory()->create(['type' => 'leasing', 'price_from' => 55, 'currency' => 'EUR']);
        $count = Space::count();
        $this->artisan('leasing:sync-minimum-prices')->assertSuccessful();
        $this->assertSame('22.00', $space->fresh()->price_from);
        $this->artisan('leasing:sync-minimum-prices --apply')->assertSuccessful();
        $this->assertSame('1500.00', $space->fresh()->price_from);
        $this->assertSame('ALL', $space->fresh()->currency);
        $this->assertSame('110.20', $space->fresh()->area_sqm);
        $this->assertSame('99.00', $other->fresh()->price_from);
        $this->assertSame('55.00', $unknown->fresh()->price_from);
        $this->assertSame($count, Space::count());
    }

    public function test_public_form_and_validation_use_unit_specific_all_minimums_in_both_languages(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        foreach (['A4' => 1500, 'D2' => 2000, 'U1' => 1000] as $code => $minimum) {
            $space = Space::factory()->published()->create(['type' => 'leasing', 'is_available' => true,
                'leasing_unit_id' => LeasingUnit::where('code', $code)->value('id'), 'area_sqm' => 51.2]);
            foreach (['al', 'en'] as $locale) {
                $slug = $space->translation($locale)->slug;
                $this->get(route('public.spaces.show', [$locale, $slug]))->assertOk()
                    ->assertSee('min="'.$minimum.'.00"', false)->assertSee('ALL')->assertDontSee('EUR');
                $this->post(route('public.spaces.leasing-request', [$locale, $slug]), ['offer_per_sqm' => $minimum - .01])
                    ->assertSessionHasErrors('offer_per_sqm');
                $this->post(route('public.spaces.leasing-request', [$locale, $slug]), ['offer_per_sqm' => $minimum])
                    ->assertSessionDoesntHaveErrors('offer_per_sqm');
            }
            $space->update(['currency' => 'ALL', 'price_from' => $minimum + 100]);
            $this->assertSame(number_format($minimum + 100, 2, '.', ''), $space->fresh()->leasingMinimumOffer());
            $this->get(route('public.spaces.show', ['en', $space->translation('en')->slug]))
                ->assertSee('min="'.($minimum + 100).'.00"', false);
            $this->post(route('public.spaces.leasing-request', ['en', $space->translation('en')->slug]), ['offer_per_sqm' => $minimum])
                ->assertSessionHasErrors('offer_per_sqm');
        }
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_editor_can_edit_the_all_minimum_in_dashboard(): void
    {
        $space = Space::factory()->published()->create(['type' => 'leasing', 'is_available' => true,
            'leasing_unit_id' => LeasingUnit::where('code', 'D2')->value('id'), 'area_sqm' => 51.2,
            'currency' => 'ALL', 'price_from' => 2000]);
        $this->actingAs(User::factory()->create())
            ->get(route('admin.leasing.edit', $space))->assertOk()->assertSee('Minimum offer per m² (ALL)');
        $data = ['status' => 'published', 'published_at' => now()->subDay()->toDateTimeString(),
            'display_order' => 0, 'currency' => 'ALL', 'booking_mode' => 'internal',
            'area_sqm' => 51.2, 'price_from' => 2200, 'is_featured' => '0', 'is_available' => '1',
            'leasing_unit_id' => $space->leasing_unit_id,
            'translations' => ['al' => ['title' => 'D2'], 'en' => ['title' => 'D2']]];
        $this->put(route('admin.leasing.update', $space), $data)->assertSessionHasNoErrors();
        $this->assertSame('2200.00', $space->fresh()->leasingMinimumOffer());
        $this->put(route('admin.leasing.update', $space), [...$data, 'currency' => 'EUR'])
            ->assertSessionHasErrors('currency');
    }

    public function test_unknown_unit_has_no_invented_minimum_and_cannot_submit_an_offer(): void
    {
        $space = Space::factory()->published()->create(['type' => 'leasing', 'is_available' => true,
            'leasing_unit_id' => LeasingUnit::where('code', 'BE1/1')->value('id'), 'area_sqm' => 10]);
        $this->get(route('public.spaces.show', ['en', $space->translation('en')->slug]))->assertOk()
            ->assertDontSee('data-leasing-form', false)->assertSee('once the minimum price is confirmed');
        $this->post(route('public.spaces.leasing-request', ['en', $space->translation('en')->slug]), ['offer_per_sqm' => 9999])
            ->assertSessionHasErrors('offer_per_sqm');
    }
}
