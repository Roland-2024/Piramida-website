<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\SectionType;
use App\Models\Media;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PresentationPageSeeder extends Seeder
{
    // Supplied shared-template placeholders, editable through Page Sections.
    private const SLIDES = [
        ['0be2fd837e46e1dbbfbe5ecb17f7d2ef1799e7c9 (1).png', 'Graphic Design', 'Explore visual composition, branding, and digital tools through practical creative projects.', 'Dizajn grafik', 'Eksploroni kompozimin vizual, identitetin e markës dhe mjetet digjitale përmes projekteve praktike krijuese.'],
        ['4d4494e51518b0723ba377d776658e219fecc676 (1).jpg', 'Music', 'Build rhythm, production, and performance skills while experimenting with sound and collaboration.', 'Muzikë', 'Zhvilloni aftësi në ritëm, prodhim dhe interpretim duke eksperimentuar me tingullin dhe bashkëpunimin.'],
        ['24fd138e4c487270d6656b18bacb04deac6199b3 (1).jpg', '3D Modeling', 'Turn ideas into dimensional objects with modeling, rendering, and digital making workflows.', 'Modelim 3D', 'Kthejini idetë në objekte tredimensionale përmes modelimit, renderimit dhe krijimit digjital.'],
        ['510df378ce3d16f0d7d9137f4390f307fe81cb0c (1).png', 'Animation', 'Learn motion, timing, and storytelling by bringing characters and visual concepts to life.', 'Animacion', 'Mësoni lëvizjen, kohëzgjatjen dhe rrëfimin duke u dhënë jetë personazheve dhe koncepteve vizuale.'],
        ['513a15bf08553429fdf22d302de80799941a90ae (1).jpg', 'Drawing', 'Strengthen observation, line, form, and expression with guided hands-on drawing practice.', 'Vizatim', 'Përmirësoni vëzhgimin, vijën, formën dhe shprehjen përmes ushtrimeve praktike të vizatimit.'],
        ['c0d7437a3950068afe0cd31c4fea121c5ccd6442 (1).jpg', 'Game Dev', 'Design playable worlds while learning game logic, interaction, prototyping, and iteration.', 'Zhvillim lojërash', 'Krijoni botë interaktive duke mësuar logjikën e lojërave, ndërveprimin, prototipimin dhe përmirësimin.'],
        ['d501635b931639cb374bb4c3479369868029ca63 (1).jpg', 'Photography', 'Practice framing, light, editing, and visual narrative through photography-based projects.', 'Fotografi', 'Praktikoni kompozimin, dritën, përpunimin dhe rrëfimin vizual përmes projekteve fotografike.'],
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ([
                ['education', 'edukim', 'Education', 'Edukim'],
                ['innovation', 'inovacion', 'Innovation', 'Inovacion'],
                ['business', 'biznes', 'Business', 'Biznes'],
                ['art', 'art-kulture', 'Art & Culture', 'Art & Kulturë'],
            ] as $order => [$slug, $alSlug, $title, $alTitle]) {
                // Never overwrite existing content, drafts, or soft-deleted pages.
                if (Page::withTrashed()->whereHas('translations', fn ($query) => $query->whereIn('slug', [$slug, $alSlug]))->exists()) {
                    continue;
                }

                $page = Page::create([
                    'status' => ContentStatus::Published,
                    'published_at' => now(),
                    'display_order' => 20 + $order,
                ]);
                $page->syncTranslations([
                    'al' => ['title' => $alTitle, 'slug' => $alSlug],
                    'en' => ['title' => $title, 'slug' => $slug],
                ]);

                foreach (self::SLIDES as $index => [$file, $heading, $description, $alHeading, $alDescription]) {
                    $source = public_path('template/images/'.$file);
                    $path = 'media/presentation/'.$file;
                    if (! Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->put($path, File::get($source));
                    }
                    [$width, $height] = getimagesize($source);
                    $media = Media::firstOrCreate(['disk' => 'public', 'path' => $path], [
                        'original_name' => $file,
                        'mime_type' => File::mimeType($source),
                        'extension' => File::extension($source),
                        'size' => File::size($source),
                        'width' => $width,
                        'height' => $height,
                        'alt_text_al' => $alHeading,
                        'alt_text_en' => $heading,
                    ]);
                    $section = $page->sections()->create([
                        'internal_name' => $title.' - template slide '.($index + 1),
                        'type' => SectionType::TextImage,
                        'primary_media_id' => $media->id,
                        'display_order' => $index,
                        'is_active' => true,
                    ]);
                    $section->syncTranslations([
                        'al' => ['title' => $alHeading, 'description' => $alDescription],
                        'en' => ['title' => $heading, 'description' => $description],
                    ]);
                }
            }
        });
    }
}
