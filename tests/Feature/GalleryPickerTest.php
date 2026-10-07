<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryPickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_removes_empty_editor_paragraphs_without_hiding_text_with_line_breaks(): void
    {
        $article = News::factory()->published()->create();
        $article->translations()->where('locale', 'en')->update(['content' => '<p>Visible<br>Still visible</p><p><br></p><p>&nbsp;</p><p>Last paragraph</p>']);
        $this->get('/en/news/news-'.$article->id)->assertOk()
            ->assertSee('Visible<br>Still visible', false)->assertSee('Last paragraph')
            ->assertDontSee('<p><br></p>', false)->assertDontSee('<p>&nbsp;</p>', false);
    }

    public function test_only_attached_images_render_in_selected_panel_in_saved_order(): void
    {
        $article = News::factory()->create();
        $images = Media::factory()->count(3)->create();
        $article->syncGallery([$images[1]->id, $images[0]->id]);
        $response = $this->actingAs(User::factory()->create())->get('/admin/news/'.$article->id.'/edit')->assertOk();
        preg_match('/data-gallery-selected.*?<button type="button" data-gallery-open/s', $response->getContent(), $match);
        $this->assertStringContainsString($images[1]->original_name, $match[0]);
        $this->assertStringNotContainsString($images[2]->original_name, $match[0]);
        $this->assertLessThan(strpos($match[0], $images[0]->original_name), strpos($match[0], $images[1]->original_name));
    }

    public function test_gallery_upload_uses_existing_authorization_and_rejects_non_images(): void
    {
        Storage::fake('public');
        $this->postJson('/admin/media', ['gallery_upload' => 1])->assertUnauthorized();
        $this->actingAs(User::factory()->create())->postJson('/admin/media', [
            'gallery_upload' => 1, 'file' => UploadedFile::fake()->image('gallery.jpg'),
        ])->assertCreated()->assertJsonStructure(['id', 'name', 'url']);
        $this->postJson('/admin/media', [
            'gallery_upload' => 1, 'file' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ])->assertUnprocessable()->assertJsonValidationErrors('file');
    }
}
