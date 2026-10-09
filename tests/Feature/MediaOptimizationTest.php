<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Services\MediaImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_keeps_original_and_creates_smaller_variants(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create())->postJson('/admin/media', [
            'file' => UploadedFile::fake()->image('large.png', 2400, 1600), 'gallery_upload' => 1,
        ])->assertCreated();
        $media = Media::firstOrFail();
        $this->assertCount(2, $media->image_variants);
        $disk = Storage::disk('public');
        $disk->assertExists($media->path);
        foreach ($media->image_variants as $width => $path) {
            $disk->assertExists($path);
            $this->assertLessThan($disk->size($media->path), $disk->size($path));
            $this->assertSame((int) $width, getimagesize($disk->path($path))[0]);
        }
        $this->assertStringEndsWith('-480.webp', $media->displayUrl(480));
        $this->assertStringEndsWith('.png', $media->url());
        $this->assertFalse(app(MediaImageOptimizer::class)->optimize($media));
    }

    public function test_private_and_animated_images_keep_the_original_url(): void
    {
        foreach ([['disk' => 'local'], ['mime_type' => 'image/gif']] as $attributes) {
            $media = Media::factory()->create($attributes);
            $this->assertFalse(app(MediaImageOptimizer::class)->optimize($media));
            $this->assertSame($media->url(), $media->displayUrl());
        }
    }

    public function test_failed_variant_storage_keeps_the_original_usable(): void
    {
        $file = UploadedFile::fake()->image('large.png', 2400, 1600);
        $media = Media::factory()->create(['path' => 'media/original.png', 'mime_type' => 'image/png']);
        $disk = \Mockery::mock();
        $disk->shouldReceive('get')->with($media->path)->andReturn(file_get_contents($file->getRealPath()));
        $disk->shouldReceive('put')->once()->andReturn(false);
        $disk->shouldReceive('url')->with($media->path)->andReturn('/storage/media/original.png');
        Storage::shouldReceive('disk')->with('public')->andReturn($disk);
        $this->assertFalse(app(MediaImageOptimizer::class)->optimize($media));
        $this->assertNull($media->fresh()->image_variants);
        $this->assertSame('/storage/media/original.png', $media->displayUrl());
    }
}
