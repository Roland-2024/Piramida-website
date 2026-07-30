<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_upload_a_valid_image_with_safe_unique_name(): void
    {
        Storage::fake('public');
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->image('hero photo.jpg', 1200, 800)->size(500),
                'alt_text_al' => 'Pamje e Piramidës',
                'alt_text_en' => 'View of Piramida',
            ])
            ->assertRedirect();

        $media = Media::query()->firstOrFail();

        Storage::disk('public')->assertExists($media->path);
        $this->assertNotSame('hero photo.jpg', basename($media->path));
        $this->assertSame(1200, $media->width);
        $this->assertSame($editor->id, $media->created_by);
    }

    public function test_upload_rejects_executable_and_oversized_files(): void
    {
        Storage::fake('public');
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
            ])
            ->assertSessionHasErrors('file');

        $this->actingAs($editor)
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->image('large.jpg')->size(10_241),
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_referenced_media_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $media = Media::factory()->create();
        Page::factory()->create(['featured_media_id' => $media->id]);

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHasErrors('media');

        $this->assertFalse($media->fresh()->trashed());
    }

    public function test_editor_cannot_delete_media(): void
    {
        $editor = User::factory()->create();
        $media = Media::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.media.destroy', $media))
            ->assertForbidden();
    }
}
