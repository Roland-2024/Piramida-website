<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_manage_global_site_settings(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'notification_email' => 'requests@piramida.test',
                'email' => 'info@piramida.test',
                'phone' => '+355 4 000 0000',
                'facebook_url' => null,
                'instagram_url' => 'https://instagram.com/piramida',
                'linkedin_url' => null,
                'translations' => [
                    'al' => [
                        'address' => 'Tirane',
                        'opening_hours' => 'E hene - E premte',
                        'footer_text' => 'Piramida',
                    ],
                    'en' => [
                        'address' => 'Tirana',
                        'opening_hours' => 'Monday - Friday',
                        'footer_text' => 'Piramida',
                    ],
                ],
            ])
            ->assertRedirect();

        $settings = SiteSetting::current();
        $this->assertSame('requests@piramida.test', $settings->notification_email);
        $this->assertSame('Tirana', $settings->translation('en', false)?->address);
    }
}
