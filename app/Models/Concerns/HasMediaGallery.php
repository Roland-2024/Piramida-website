<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMediaGallery
{
    public function gallery(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'attachable', 'media_attachments')
            ->withPivot(['role', 'display_order'])
            ->wherePivot('role', 'gallery')
            ->orderByPivot('display_order')
            ->orderBy('media.id');
    }

    /**
     * @param  array<int, int|string>  $mediaIds
     */
    public function syncGallery(array $mediaIds): void
    {
        $this->gallery()->sync(
            collect($mediaIds)
                ->filter()
                ->unique()
                ->values()
                ->mapWithKeys(fn (int|string $id, int $order) => [
                    (int) $id => ['role' => 'gallery', 'display_order' => $order],
                ])
                ->all()
        );
    }
}
