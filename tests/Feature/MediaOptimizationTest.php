<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Services\MediaImageOptimizer;
use App\Support\WebsiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
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
        $this->assertStringContainsString('-480.webp 480w', $media->srcset());
        $this->assertStringContainsString('-1600.webp 1600w', $media->srcset());
        $this->assertStringEndsWith('.png', $media->url());
        $this->assertFalse(app(MediaImageOptimizer::class)->optimize($media));
    }

    public function test_private_and_animated_images_keep_the_original_url(): void
    {
        foreach ([['disk' => 'local'], ['mime_type' => 'image/gif']] as $attributes) {
            $media = Media::factory()->create($attributes);
            $this->assertFalse(app(MediaImageOptimizer::class)->optimize($media));
            $this->assertSame($media->url(), $media->displayUrl());
            $this->assertNull($media->srcset());
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

    public function test_responsive_markup_uses_actual_widths_and_keeps_loading_controls(): void
    {
        $media = Media::factory()->create(['width' => 800, 'height' => 600, 'image_variants' => [480 => 'small.webp', 1600 => 'large.webp']]);
        $this->assertSame(Storage::disk('public')->url('small.webp').' 480w, '.Storage::disk('public')->url('large.webp').' 800w', $media->srcset());
        $html = Blade::render('<x-public-image :media="$media" alt="Example" sizes="300px" loading="lazy" data-gallery-image :hidden="true" />', compact('media'));
        $this->assertStringContainsString('sizes="300px"', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringContainsString('decoding="async"', $html);
        $this->assertStringContainsString('width="800"', $html);
        $this->assertStringContainsString('height="600"', $html);
        $this->assertStringContainsString('hidden', $html);
        $this->assertStringContainsString('data-gallery-image', $html);

        $media->image_variants = [480 => 'small.webp'];
        $this->assertStringEndsWith($media->url().' 800w', $media->srcset());
        $media->width = 320;
        $this->assertSame(Storage::disk('public')->url('small.webp').' 320w', $media->srcset());
        $media->image_variants = null;
        $this->assertNull($media->srcset());
        $html = Blade::render('<x-public-image :media="$media" path="template/images/piramida-hero-1920.jpg" alt="Fallback" />', compact('media'));
        $this->assertStringContainsString('src="'.$media->url().'"', $html);
        $this->assertStringNotContainsString('srcset=', $html);
    }

    public function test_template_copies_are_smaller_and_image_overrides_keep_their_own_candidates(): void
    {
        foreach (config('template_photos') as $path) {
            $copy = substr($path, 0, strrpos($path, '.')).'.webp';
            $this->assertFileExists(public_path($copy));
            $this->assertLessThan(filesize(public_path($path)), filesize(public_path($copy)));
            $this->assertSame(array_slice(getimagesize(public_path($path)), 0, 2), array_slice(getimagesize(public_path($copy)), 0, 2));
            $this->assertStringEndsWith($copy, WebsiteContent::image($path));
            $this->assertStringEndsWith($path, WebsiteContent::image($path, false));
        }
        $this->get('/')->assertOk()->assertSee('fetchpriority="high"', false)
            ->assertSee('piramida-hero-960.jpg 960w', false)->assertSee('loading="lazy"', false)
            ->assertSee('class="absolute inset-0 w-full h-full object-cover"', false);

        $media = Media::factory()->create(['width' => 1600, 'height' => 1000, 'image_variants' => [480 => 'hero-small.webp', 1600 => 'hero-large.webp']]);
        DB::table('website_images')->insert(['key' => sha1('template/images/piramida-hero-1920.jpg'), 'media_id' => $media->id]);
        $this->app->forgetScopedInstances();
        $this->get('/en')->assertOk()->assertSee('hero-small.webp 480w', false)
            ->assertSee('hero-large.webp 1600w', false)->assertDontSee('piramida-hero-960.jpg', false);
        $this->assertSame($media->url(), WebsiteContent::image('template/images/piramida-hero-1920.jpg', false));
    }
}
