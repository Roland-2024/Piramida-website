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

    public function test_library_is_paginated_searchable_and_excludes_private_or_deleted_media(): void
    {
        $this->getJson('/admin/media/picker')->assertUnauthorized();
        Media::factory()->count(25)->create();
        Media::factory()->create(['disk' => 'local', 'original_name' => 'private.jpg']);
        Media::factory()->create(['mime_type' => 'application/pdf']);
        Media::factory()->create()->delete();
        $this->actingAs(User::factory()->create());
        $first = $this->getJson('/admin/media/picker')->assertOk()->assertJsonCount(24, 'images');
        $this->getJson($first->json('next_url'))->assertOk()->assertJsonCount(1, 'images');
        $this->getJson('/admin/media/picker?search=private')->assertOk()->assertJsonCount(0, 'images');
        $article = News::factory()->create();
        $this->get(route('admin.news.edit', $article))->assertOk()->assertDontSee('data-media-id=', false);
    }

    public function test_failed_storage_does_not_create_a_media_record(): void
    {
        $disk = \Mockery::mock();
        $disk->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->andReturn($disk);
        $this->actingAs(User::factory()->create())->postJson('/admin/media', [
            'gallery_upload' => 1, 'file' => UploadedFile::fake()->image('gallery.jpg'),
        ])->assertUnprocessable()->assertJsonValidationErrors('file');
        $this->assertDatabaseCount('media', 0);
    }

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
        preg_match('/data-gallery-selected class="gallery-picker-grid".*?<button type="button" data-gallery-open/s', $response->getContent(), $match);
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
        ])->assertCreated()->assertJsonStructure(['id', 'name', 'url', 'edit_url']);
        $this->postJson('/admin/media', [
            'gallery_upload' => 1, 'file' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ])->assertUnprocessable()->assertJsonValidationErrors('file');
    }

    public function test_featured_image_picker_preserves_selection_and_can_replace_or_remove_it(): void
    {
        $images = Media::factory()->count(2)->create();
        $article = News::factory()->create(['featured_media_id' => $images[0]->id]);
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.news.edit', $article))->assertOk()
            ->assertSee('data-single-image', false)->assertSee('Set / change image')
            ->assertSee('Use selected image')->assertSee('data-gallery-upload', false)
            ->assertSee('name="featured_media_id" value="'.$images[0]->id.'"', false);
        $data = ['status' => 'draft', 'translations' => [
            'al' => ['title' => 'Titull', 'slug' => 'titull'], 'en' => ['title' => 'Title', 'slug' => 'title'],
        ]];
        foreach ([$images[1]->id, null] as $id) {
            $this->put(route('admin.news.update', $article), [...$data, 'featured_media_id' => $id])->assertSessionHasNoErrors();
            $this->assertSame($id, $article->fresh()->featured_media_id);
        }
        $this->assertDatabaseCount('media', 2);
    }
}
