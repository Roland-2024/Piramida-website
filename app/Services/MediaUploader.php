<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MediaUploader
{
    /**
     * @param  array{alt_text_al?: string|null, alt_text_en?: string|null}  $metadata
     */
    public function upload(UploadedFile $file, User $user, array $metadata): Media
    {
        $disk = config('filesystems.default');
        $path = $file->store('media/'.now()->format('Y/m'), $disk);
        [$width, $height] = $this->dimensions($file);

        try {
            return Media::query()->create([
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => strtolower($file->extension()),
                'size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'alt_text_al' => $metadata['alt_text_al'] ?? null,
                'alt_text_en' => $metadata['alt_text_en'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($path);

            throw $exception;
        }
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function dimensions(UploadedFile $file): array
    {
        if (! str_starts_with((string) $file->getMimeType(), 'image/')) {
            return [null, null];
        }

        $dimensions = @getimagesize($file->getRealPath());

        return $dimensions === false
            ? [null, null]
            : [$dimensions[0], $dimensions[1]];
    }
}
