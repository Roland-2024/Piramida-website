<?php

namespace App\Http\Controllers\PublicSite;

use App\Enums\SectionType;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $locale, string $slug): View
    {
        $page = Page::query()
            ->published()
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with([
                'translations',
                'featuredMedia',
                'sections' => fn (HasMany $query) => $query
                    ->active()
                    ->with(['translations', 'primaryMedia', 'secondaryMedia', 'gallery']),
            ])
            ->firstOrFail();

        // Draft template mapping; keep other CMS pages on the generic section renderer.
        $view = match (true) {
            $page->translations->contains('slug', 'about-us') => 'public.pages.about',
            $page->translations->whereIn('slug', ['education', 'innovation', 'business', 'art'])->isNotEmpty() => 'public.pages.education',
            default => 'public.pages.show',
        };

        return view($view, [
            'page' => $page,
            'translation' => $page->translation($locale, false),
            'languageUrls' => $this->languageUrls($page),
            'slides' => $page->sections
                ->whereIn('type', [SectionType::TextImage, SectionType::Gallery])
                ->flatMap(function ($section) use ($locale, $page) {
                    $content = $section->translation($locale);
                    $images = $section->gallery->isNotEmpty()
                        ? $section->gallery
                        : collect([$section->primaryMedia])->filter();

                    return $images->map(fn ($image) => [
                        'url' => $image->url(),
                        'title' => ($images->count() > 1 ? ($locale === 'en' ? $image->alt_text_en : $image->alt_text_al) : null)
                            ?: $content?->subtitle ?: $content?->title ?: $page->translation($locale)->title,
                        'description' => strip_tags($content?->description ?? ''),
                    ]);
                })->values(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function languageUrls(Page $page): array
    {
        return collect(config('cms.locales'))
            ->mapWithKeys(function (string $name, string $locale) use ($page): array {
                $translation = $page->translation($locale, false);

                return [
                    $locale => $translation
                        ? route('public.pages.show', [$locale, $translation->slug])
                        : route('public.home', $locale),
                ];
            })
            ->all();
    }
}
