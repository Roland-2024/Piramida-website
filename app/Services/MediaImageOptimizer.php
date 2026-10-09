<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class MediaImageOptimizer
{
    public function optimize(Media $media): bool
    {
        if ($media->disk !== 'public' || ! in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/webp'], true) || $media->image_variants) {
            return false;
        }
        $disk = Storage::disk($media->disk);
        $paths = [];
        try {
            $contents = $disk->get($media->path);
            $size = $contents ? @getimagesizefromstring($contents) : false;
            // Bound GD memory consumption; oversized originals remain available unchanged.
            if (! $size || $size[0] * $size[1] > 16000000 || ! function_exists('imagewebp')) {
                return false;
            }
            // Preserve animation instead of flattening animated WebP uploads.
            if ($media->mime_type === 'image/webp' && str_contains($contents, 'ANIM')) {
                return false;
            }
            $source = @imagecreatefromstring($contents);
            if (! $source) {
                return false;
            }
            // Do not strip orientation metadata from portrait/rotated JPEG originals.
            if ($media->mime_type === 'image/jpeg') {
                if (! function_exists('exif_read_data')) {
                    return false;
                }
                $metadata = fopen('php://temp', 'w+b');
                try {
                    fwrite($metadata, $contents);
                    rewind($metadata);
                    $exif = @exif_read_data($metadata);
                } finally {
                    fclose($metadata);
                }
                if (($exif['Orientation'] ?? 1) !== 1) {
                    return false;
                }
            }
            foreach ([480, 1600] as $width) {
                $targetWidth = min($width, imagesx($source));
                $targetHeight = max(1, (int) round(imagesy($source) * $targetWidth / imagesx($source)));
                $target = imagecreatetruecolor($targetWidth, $targetHeight);
                imagealphablending($target, false);
                imagesavealpha($target, true);
                imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($source), imagesy($source));
                $stream = fopen('php://temp', 'w+b');
                try {
                    if (! imagewebp($target, $stream, 82)) {
                        throw new RuntimeException('Image encoding failed.');
                    }
                    rewind($stream);
                    $bytes = stream_get_contents($stream);
                } finally {
                    fclose($stream);
                }
                if (strlen($bytes) >= strlen($contents)) {
                    continue;
                }
                $path = 'media/variants/'.$media->id.'/'.sha1($contents).'-'.$width.'.webp';
                if (! $disk->put($path, $bytes)) {
                    throw new RuntimeException('Image variant could not be saved.');
                }
                $paths[$width] = $path;
            }
            $media->forceFill(['image_variants' => $paths])->saveQuietly();

            return $paths !== [];
        } catch (Throwable $exception) {
            foreach ($paths as $path) {
                $disk->delete($path);
            }
            report($exception);

            return false;
        }
    }
}
