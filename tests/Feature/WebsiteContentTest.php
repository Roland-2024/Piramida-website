<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_manage_bilingual_public_text_and_nested_seo_without_html_execution(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['cms', 'website', 'seo', 'validation', 'pagination', 'images', 'contact'] as $group) {
            $this->get(route('admin.website-content.edit', ['group' => $group]))->assertOk();
        }
        $this->put(route('admin.website-content.update'), ['group' => 'website', 'texts' => [
            sha1('website.partials_footer_company') => ['al' => 'Kompania jonë', 'en' => '<script>alert(1)</script>'],
        ]])->assertSessionHasNoErrors()->assertRedirect();
        $this->get('/')->assertOk()->assertSee('Kompania jonë');
        $this->get('/en')->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->put(route('admin.website-content.update'), ['group' => 'seo', 'texts' => [
            sha1('seo.pages.events.index.0') => ['al' => 'Eventet e komunitetit', 'en' => 'Community events'],
        ]])->assertSessionHasNoErrors();
        $this->get('/en/events')->assertOk()->assertSee('Community events');
        $this->get('/events')->assertOk()->assertSee('Eventet e komunitetit');
        $this->get(route('admin.settings.edit'))->assertForbidden();
    }

    public function test_content_updates_require_authorization_known_keys_and_placeholders(): void
    {
        $this->put(route('admin.website-content.update'), [])->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create());
        $this->put(route('admin.website-content.update'), ['group' => 'website', 'texts' => [
            sha1('website.copyright') => ['al' => 'Missing year'],
        ]])->assertSessionHasErrors('texts.'.sha1('website.copyright').'.al');
        $this->put(route('admin.website-content.update'), ['group' => 'website', 'texts' => ['unknown' => ['al' => 'invalid']]])->assertSessionHasErrors('texts');
    }

    public function test_editor_can_replace_images_but_not_expose_private_files_and_referenced_media_is_protected(): void
    {
        $this->actingAs(User::factory()->create());
        $image = Media::factory()->create();
        $private = Media::factory()->create(['disk' => 'local']);
        $key = sha1('template/images/piramida-hero-1920.jpg');
        $this->put(route('admin.website-content.update'), ['group' => 'images', 'images' => [$key => $private->id]])->assertSessionHasErrors('images.'.$key);
        $this->put(route('admin.website-content.update'), ['group' => 'images', 'images' => [$key => $image->id]])->assertSessionHasNoErrors();
        $this->assertTrue($image->isReferenced());
        Page::factory()->published()->create(['is_homepage' => true]);
        $this->app->forgetScopedInstances();
        $this->get('/')->assertOk()->assertSee($image->url(), false)->assertDontSee('piramida-hero-960.jpg 960w', false);
        $this->put(route('admin.website-content.update'), ['group' => 'images', 'images' => [$key => null]])->assertSessionHasNoErrors();
        $this->assertFalse($image->isReferenced());
    }

    public function test_public_contact_update_cannot_change_mail_configuration(): void
    {
        $settings = SiteSetting::current();
        $settings->update(['notification_email' => 'staff@example.test']);
        $this->actingAs(User::factory()->create())->put(route('admin.website-content.contact'), [
            'email' => 'public@example.test', 'notification_email' => 'attacker@example.test', 'postmark_enabled' => true,
            'translations' => ['al' => ['address' => 'Tiranë'], 'en' => ['address' => 'Tirana']],
        ])->assertSessionHasNoErrors();
        $this->assertSame('public@example.test', $settings->fresh()->email);
        $this->assertSame('staff@example.test', $settings->fresh()->notification_email);
        $this->assertFalse((bool) $settings->fresh()->postmark_enabled);
    }
}
