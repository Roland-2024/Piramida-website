<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\MediaImageOptimizer;
use Illuminate\Console\Command;

class OptimizeMedia extends Command
{
    protected $signature = 'media:optimize';

    protected $description = 'Create smaller public image variants without changing originals';

    public function handle(MediaImageOptimizer $optimizer): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('Enable PHP GD with WebP support before optimizing images.');

            return self::FAILURE;
        }
        $count = 0;
        Media::query()->where('disk', 'public')->whereNull('image_variants')->chunkById(50, function ($items) use ($optimizer, &$count) {
            foreach ($items as $media) {
                $count += (int) $optimizer->optimize($media);
            }
        });
        $this->info("Optimized {$count} images. Originals retained; unsupported images use originals.");

        return self::SUCCESS;
    }
}
