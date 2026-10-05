<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostmarkSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_save_encrypted_credentials_and_blank_fields_preserve_them(): void
    {
        $data = [
            'postmark_enabled' => '1',
            'postmark_username' => 'test-access-key',
            'postmark_password' => 'test-secret-key',
            'mail_from_address' => 'sender@example.test',
            'mail_from_name' => 'Piramida',
            'translations' => ['al' => ['address' => 'Tirane'], 'en' => ['address' => 'Tirana']],
        ];
        $this->actingAs(User::factory()->create())->put(route('admin.settings.update'), $data)->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->put(route('admin.settings.update'), $data)->assertSessionHasNoErrors();
        $settings = SiteSetting::current();
        $this->assertSame('test-secret-key', $settings->postmark_password);
        $this->assertNotSame('test-secret-key', DB::table('site_settings')->value('postmark_password'));
        $this->assertNotSame('test-access-key', DB::table('site_settings')->value('postmark_username'));
        $this->assertArrayNotHasKey('postmark_password', $settings->toArray());
        $this->get(route('admin.settings.edit'))->assertDontSee('test-secret-key')->assertDontSee('test-access-key');
        $data['postmark_username'] = $data['postmark_password'] = '';
        $this->put(route('admin.settings.update'), $data)->assertSessionHasNoErrors();
        $this->assertSame('test-secret-key', $settings->fresh()->postmark_password);
        $data['postmark_password'] = 'replacement-secret';
        $data['mail_from_address'] = 'invalid';
        $this->put(route('admin.settings.update'), $data)->assertSessionHasErrors('mail_from_address');
        $this->assertNull(session('_old_input.postmark_password'));
        $this->assertNull(session('_old_input.postmark_username'));
    }

    public function test_enabling_postmark_requires_credentials_and_sender(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.settings.update'), ['postmark_enabled' => '1'])
            ->assertSessionHasErrors(['postmark_username', 'postmark_password', 'mail_from_address', 'mail_from_name']);
    }
}
