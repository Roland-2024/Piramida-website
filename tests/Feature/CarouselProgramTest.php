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
