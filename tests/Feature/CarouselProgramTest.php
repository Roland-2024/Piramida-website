<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarouselProgramTest extends TestCase
{
    use RefreshDatabase;

    public function test_optional_carousel_link_can_be_saved_rendered_and_removed(): void
    {
        $page = Page::factory()->published()->create();
        $page->translations()->where('locale', 'en')->update(['slug' => 'education']);
        $this->actingAs(User::factory()->create());
        $this->get('/admin/programs/create')->assertSee('name="external_url"', false);
        $data = [
            'category' => 'education', 'status' => 'published', 'published_at' => now()->subDay()->toDateTimeString(),
            'display_order' => 0, 'external_url' => 'https://example.test/college',
            'translations' => ['al' => ['title' => 'Kolegji'], 'en' => ['title' => 'College']],
        ];
        $this->post('/admin/programs', $data)->assertSessionHasNoErrors();
        $post = Program::firstOrFail();
        foreach (['en', 'al'] as $locale) {
            $this->get(route('public.pages.show', [$locale, $page->translation($locale)->slug]))
                ->assertOk()->assertSee('href="https://example.test/college"', false);
        }
        foreach (['javascript:alert(1)', 'ftp://example.test/file', 'not a url'] as $invalid) {
            $this->put(route('admin.programs.update', $post), [...$data, 'external_url' => $invalid])
                ->assertSessionHasErrors('external_url');
        }
        $post->update(['external_url' => 'javascript:alert(1)']);
        $this->get('/en/education')->assertDontSee('href="javascript:', false);
        $this->put(route('admin.programs.update', $post), [...$data, 'external_url' => ''])
            ->assertSessionHasNoErrors();
        $this->assertNull($post->fresh()->external_url);
        $this->get('/en/education')->assertDontSee('href="https://example.test/college"', false);
    }

    public function test_editor_can_create_and_draft_carousel_posts(): void
    {
        $page = Page::factory()->published()->create();
        $page->translations()->where('locale', 'en')->update(['slug' => 'education']);
        $editor = User::factory()->create();
        $this->actingAs($editor)->get('/admin/programs/create')->assertOk();
        $data = [
            'category' => 'education', 'status' => 'published', 'published_at' => now()->subDay()->toDateTimeString(),
            'display_order' => 0, 'booking_mode' => 'none', 'is_featured' => 0,
            'translations' => ['al' => ['title' => 'Postim i ri', 'description' => 'Shqip'], 'en' => ['title' => 'New slide', 'description' => 'English']],
        ];
        $this->post('/admin/programs', $data)->assertSessionHasNoErrors()->assertRedirect();
        $post = Program::firstOrFail();
        $this->get('/en/education')->assertOk()->assertSee('New slide');
        Program::factory()->published()->create(['category' => 'education', 'published_at' => now()->addDay()]);
        Program::factory()->published()->create(['category' => 'innovation']);
        $this->get('/en/education')->assertViewHas('slides', fn ($slides) => $slides->count() === 1);
        $data['status'] = 'draft';
        $this->put('/admin/programs/'.$post->id, $data)->assertSessionHasNoErrors();
        $this->get('/en/education')->assertViewHas('slides', fn ($slides) => $slides->isEmpty());
        $this->delete('/admin/programs/'.$post->id)->assertForbidden();
    }
}
