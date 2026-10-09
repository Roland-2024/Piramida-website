<?php

namespace App\View\Components\Admin;

use App\Models\Media;
use Illuminate\View\Component;
use Illuminate\View\View;

class GalleryPicker extends Component
{
    public array $ids;

    public function __construct(
        public mixed $selected = [],
        public string $label = 'Gallery images',
        public bool $single = false,
        public string $name = 'gallery_media_ids',
    ) {
        $this->ids = array_values(array_filter(array_map('strval', (array) old($name, $selected))));
    }

    public function render(): View
    {
        return view('components.admin.gallery-picker', [
            'images' => Media::query()->whereIn('id', $this->ids)->where('disk', 'public')->where('mime_type', 'like', 'image/%')->get(),
        ]);
    }
}
