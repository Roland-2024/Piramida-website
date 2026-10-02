<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BusinessLogoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['dv8' => 'Attractions/dv8_logo.jpg', 'mulliri' => 'Attractions/mulliri_logo.jpg', 'banas' => 'Attractions/banas_logo.jpg', 'piramida-store' => 'logo piramida.svg'] as $slug => $file) {
            $business = Business::whereRelation('translations', 'slug', $slug)->whereNull('logo_media_id')->first();
            if (! $business) {
                continue;
            }
            $source = public_path('template/images/'.$file);
            $path = 'media/demo/logos/'.basename($file);
            Storage::disk('public')->put($path, file_get_contents($source));
            $media = Media::firstOrCreate(['disk' => 'public', 'path' => $path], [
                'original_name' => basename($file),
                'mime_type' => str_ends_with($file, '.svg') ? 'image/svg+xml' : 'image/jpeg',
                'extension' => pathinfo($file, PATHINFO_EXTENSION),
                'size' => filesize($source),
                'alt_text_al' => $slug.' logo (demo)',
                'alt_text_en' => $slug.' logo (demo)',
            ]);
            $business->update(['logo_media_id' => $media->id]);
        }
    }
}
