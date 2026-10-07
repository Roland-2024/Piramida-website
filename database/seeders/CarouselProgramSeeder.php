<?php

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarouselProgramSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (['education' => 'education', 'innovation' => 'innovation', 'art' => 'art_culture'] as $slug => $category) {
                // This is a one-time import per category, never a synchronization or reset.
                if (Program::withTrashed()->where('category', $category)->exists()) {
                    continue;
                }
                $page = Page::whereHas('translations', fn ($query) => $query->where('locale', 'en')->where('slug', $slug))
                    ->with(['translations', 'sections.translations', 'sections.primaryMedia', 'sections.gallery'])->first();
                if (! $page) {
                    continue;
                }
                $order = 0;
                foreach ($page->sections->whereIn('type', [SectionType::TextImage, SectionType::Gallery]) as $section) {
                    $images = $section->gallery->isNotEmpty() ? $section->gallery : collect([$section->primaryMedia])->filter();
                    foreach ($images as $image) {
                        $translations = [];
                        foreach (['al', 'en'] as $locale) {
                            $content = $section->translation($locale, false);
                            if (! $content) {
                                continue;
                            }
                            $translations[$locale] = [
                                'title' => ($images->count() > 1 ? $image->{'alt_text_'.$locale} : null) ?: $content->subtitle ?: $content->title ?: $page->translation($locale)?->title,
                                'slug' => 'carousel-'.$section->id.'-'.$image->id,
                                'description' => $content->description,
                            ];
                        }
                        if (! $translations) {
                            continue;
                        }
                        $program = Program::create([
                            'category' => $category, 'featured_media_id' => $image->id,
                            'status' => $section->is_active ? $page->status : 'draft',
                            'published_at' => $page->published_at, 'booking_mode' => 'none',
                            'display_order' => $order++, 'created_by' => $page->created_by, 'updated_by' => $page->updated_by,
                        ]);
                        $program->syncTranslations($translations);
                    }
                }
            }
        });
    }
}
