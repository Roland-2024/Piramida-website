<?php

namespace Tests\Feature;

use App\Http\Requests\Admin\PageSectionRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class HistoryVideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_rejects_unsupported_or_unsafe_video_urls(): void
    {
        $request = PageSectionRequest::create('/', 'POST', ['internal_name' => 'About - History']);
        foreach (['javascript:alert(1)', 'https://example.com/page', ['bad']] as $url) {
            $this->assertTrue(Validator::make(['video_url' => $url], ['video_url' => $request->rules()['video_url']])->fails());
        }
    }

    public function test_history_video_uses_lazy_dialog_and_hides_inactive_section_video(): void
    {
        $page = Page::factory()->published()->create();
        $page->translations()->where('locale', 'en')->update(['slug' => 'about-us']);
        $section = PageSection::factory()->for($page)->create([
            'internal_name' => 'About - History', 'video_url' => 'https://www.youtube.com/watch?v=8f-I2EdcvRI',
        ]);
        $this->get('/en/about-us')->assertOk()->assertSee('data-dialog-open="historyVideoDialog"', false)
            ->assertSee('data-src="https://www.youtube-nocookie.com/embed/8f-I2EdcvRI?autoplay=1"', false);
        $section->update(['video_url' => '/storage/history.mp4']);
        $this->get('/en/about-us')->assertOk()->assertSee('<video data-src="/storage/history.mp4"', false);
        $section->update(['is_active' => false]);
        $this->get('/en/about-us')->assertOk()->assertDontSee('historyVideoDialog');
    }

    public function test_editor_can_upload_video_and_referenced_video_is_protected(): void
    {
        Storage::fake('public');
        $editor = User::factory()->create();
        $this->actingAs($editor)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->create('history.mp4', 500, 'video/mp4'),
        ])->assertSessionHasNoErrors()->assertRedirect();
        $video = Media::firstOrFail();
        Storage::disk($video->disk)->assertExists($video->path);
        PageSection::factory()->create(['video_url' => $video->url()]);
        $this->actingAs(User::factory()->admin()->create())->delete(route('admin.media.destroy', $video))
            ->assertSessionHasErrors('media');
        $this->assertFalse($video->fresh()->trashed());
        $this->actingAs($editor)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->create('fake.mp4', 10, 'application/x-php'),
        ])->assertSessionHasErrors('file');
    }
}
