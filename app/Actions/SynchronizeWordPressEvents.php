<?php

namespace App\Actions;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\EventCategory;
use App\Models\Event;
use App\Models\EventTranslation;
use App\Models\Media;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class SynchronizeWordPressEvents
{
    /** @var array<int, string> */
    private array $warnings = [];

    /** @var array<string, Media|null> */
    private array $media = [];

    /**
     * @return array{created: int, updated: int, drafted: int, skipped: int, warnings: array<int, string>}
     */
    public function handle(): array
    {
        $this->warnings = [];
        $this->media = [];

        $posts = [];

        foreach (array_keys(config('cms.locales')) as $locale) {
            foreach ($this->fetch($locale) as $post) {
                $post['_locale'] = $locale;
                $posts[] = $post;
            }
        }

        $mappings = EventTranslation::query()
            ->whereNotNull('wordpress_id')
            ->get()
            ->keyBy('wordpress_id');

        $result = ['created' => 0, 'updated' => 0, 'drafted' => 0, 'skipped' => 0];
        $groups = collect($posts)->groupBy(fn (array $post): string => $this->groupKey($post));

        foreach ($groups as $group) {
            $byLocale = $group->groupBy('_locale');

            if ($byLocale->contains(fn ($items): bool => $items->count() > 1)) {
                $result['skipped']++;
                $this->warnings[] = 'Skipped an ambiguous WordPress image match.';

                continue;
            }

            $eventIds = $group
                ->map(fn (array $post): ?int => $mappings->get((int) $post['id'])?->event_id)
                ->filter()
                ->unique()
                ->values();

            if ($eventIds->count() > 1) {
                $result['skipped']++;
                $this->warnings[] = 'Skipped conflicting WordPress event mappings.';

                continue;
            }

            if ($eventIds->isEmpty() && $byLocale->keys()->sort()->values()->all() !== collect(array_keys(config('cms.locales')))->sort()->values()->all()) {
                $result['skipped']++;
                $this->warnings[] = 'Skipped an unpaired WordPress event.';

                continue;
            }

            $event = $eventIds->isNotEmpty()
                ? Event::withTrashed()->find($eventIds->first())
                : null;

            try {
                $created = $event === null;
                $this->syncGroup($event, $group->all());
                $result[$created ? 'created' : 'updated']++;
            } catch (Throwable $exception) {
                report($exception);
                $result['skipped']++;
                $this->warnings[] = 'Skipped WordPress event '.($group->first()['id'] ?? 'unknown').': '.$exception->getMessage();
            }
        }

        $seenIds = collect($posts)->pluck('id')->map(fn ($id): int => (int) $id);

        Event::query()
            ->whereHas('translations', fn ($query) => $query->whereNotNull('wordpress_id'))
            ->with('translations')
            ->get()
            ->each(function (Event $event) use ($seenIds, &$result): void {
                $sourceIds = $event->translations->pluck('wordpress_id')->filter();

                if ($sourceIds->intersect($seenIds)->isEmpty() && $event->status !== ContentStatus::Draft) {
                    $event->update(['status' => ContentStatus::Draft, 'published_at' => null]);
                    $result['drafted']++;
                }
            });

        return [...$result, 'warnings' => $this->warnings];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetch(string $locale): array
    {
        $posts = [];
        $page = 1;

        do {
            $response = $this->client()->get('wp/v2/event', [
                'lang' => $locale,
                'per_page' => 100,
                'page' => $page,
            ])->throw();

            $items = $response->json();

            if (! is_array($items) || ! array_is_list($items)) {
                throw new RuntimeException("WordPress returned invalid {$locale} event data.");
            }

            $posts = [...$posts, ...$items];
            $totalPages = max(1, (int) $response->header('X-WP-TotalPages'));
            $page++;
        } while ($page <= $totalPages);

        return $posts;
    }

    private function client(): PendingRequest
    {
        $url = rtrim((string) config('services.wordpress_events.url'), '/');
        $key = trim((string) config('services.wordpress_events.api_key'));

        if ($url === '' || $key === '') {
            throw new RuntimeException('WordPress event URL and API key must be configured.');
        }

        return Http::baseUrl($url)
            ->acceptJson()
            ->withHeaders(['Api-Key' => $key])
            ->timeout(30)
            ->retry(2, 250);
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function groupKey(array $post): string
    {
        $image = trim((string) ($post['featured_image_url'] ?? ''));

        if ($image === '') {
            return "wordpress:{$post['_locale']}:{$post['id']}";
        }

        return strtolower((string) preg_replace('/[?#].*$/', '', $image));
    }

    /**
     * @param  array<int, array<string, mixed>>  $posts
     */
    private function syncGroup(?Event $event, array $posts): Event
    {
        $postsByLocale = collect($posts)->keyBy('_locale');
        $start = $this->firstDate($posts, '_event_start_date');

        if ($start === null) {
            throw new RuntimeException('The event has no valid start date.');
        }

        $end = $this->firstDate($posts, '_event_end_date') ?? $start->copy();
        $published = collect($posts)->contains(fn (array $post): bool => ($post['status'] ?? null) === 'publish');
        $externalUrl = collect($posts)
            ->map(fn (array $post): ?string => $this->meta($post, '_event_join_url'))
            ->first(fn (?string $url): bool => filter_var($url, FILTER_VALIDATE_URL) !== false);
        $media = $this->mediaFor($postsByLocale->first(), $postsByLocale);
        $publishedAt = $published ? $this->publishedAt($posts) : null;

        return DB::transaction(function () use ($event, $posts, $start, $end, $published, $externalUrl, $media, $publishedAt): Event {
            $attributes = [
                'featured_media_id' => $media?->id ?? $event?->featured_media_id,
                'category' => EventCategory::Event,
                'status' => $published ? ContentStatus::Published : ContentStatus::Draft,
                'published_at' => $publishedAt,
                'starts_at' => $start,
                'ends_at' => $end,
                'external_url' => $externalUrl,
                'booking_mode' => $externalUrl ? BookingMode::External : BookingMode::None,
                'is_featured' => collect($posts)->contains(fn (array $post): bool => $this->isFeatured($post)),
                'wordpress_synced_at' => now(),
            ];

            if ($event) {
                $event->update($attributes);
            } else {
                $event = Event::query()->create([...$attributes, 'display_order' => 0]);
            }

            foreach ($posts as $post) {
                $locale = (string) $post['_locale'];
                $translation = $event->translations()->firstOrNew(['locale' => $locale]);
                $title = $this->plainText(data_get($post, 'title.rendered')) ?: 'WordPress event '.$post['id'];

                $translation->fill([
                    'wordpress_id' => (int) $post['id'],
                    'title' => $title,
                    'slug' => $this->uniqueSlug($locale, (string) ($post['slug'] ?? $title), (int) $post['id'], $translation->id),
                    'short_description' => $this->plainText(data_get($post, 'excerpt.rendered')),
                    'description' => $this->description($post, $locale),
                    'location' => $this->meta($post, '_event_location'),
                    'seo_title' => $title,
                    'seo_description' => $this->plainText(data_get($post, 'excerpt.rendered')),
                ])->save();
            }

            return $event;
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $posts
     */
    private function firstDate(array $posts, string $key): ?Carbon
    {
        foreach ($posts as $post) {
            $value = $this->meta($post, $key);

            if ($value === null) {
                continue;
            }

            try {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}(?: \d{2}:\d{2}(?::\d{2})?)?$/', $value)) {
                    $format = strlen($value) === 10 ? 'd/m/Y' : (strlen($value) === 16 ? 'd/m/Y H:i' : 'd/m/Y H:i:s');

                    return Carbon::createFromFormat($format, $value, config('app.timezone'));
                }

                return Carbon::parse($value, config('app.timezone'));
            } catch (Throwable) {
                // Try the paired translation before skipping the event.
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $posts
     */
    private function publishedAt(array $posts): Carbon
    {
        foreach ($posts as $post) {
            $value = $post['date_gmt'] ?? $post['date'] ?? null;

            if ($value) {
                try {
                    return Carbon::parse($value, isset($post['date_gmt']) ? 'UTC' : config('app.timezone'));
                } catch (Throwable) {
                    // Fall back to the current time.
                }
            }
        }

        return now();
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function meta(array $post, string $key): ?string
    {
        $value = data_get($post, "meta.{$key}", $post[$key] ?? null);
        $value = is_array($value) ? reset($value) : $value;
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function description(array $post, string $locale): ?string
    {
        $description = trim((string) data_get($post, 'content.rendered', ''));
        $labels = $locale === 'al'
            ? ['_event_organizer' => 'Organizatori', '_event_duration' => 'Kohëzgjatja', '_event_agenda' => 'Programi']
            : ['_event_organizer' => 'Organizer', '_event_duration' => 'Duration', '_event_agenda' => 'Agenda'];

        foreach ($labels as $key => $label) {
            $value = $this->meta($post, $key);

            if ($value === null) {
                continue;
            }

            $safeValue = e($value);

            if ($key === '_event_agenda' && filter_var($value, FILTER_VALIDATE_URL)) {
                $safeValue = '<a href="'.$safeValue.'">'.$safeValue.'</a>';
            }

            $description .= '<p><strong>'.e($label).':</strong> '.$safeValue.'</p>';
        }

        return $description === '' ? null : $description;
    }

    private function plainText(mixed $value): ?string
    {
        $text = trim(html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $text === '' ? null : $text;
    }

    private function uniqueSlug(string $locale, string $value, int $wordpressId, ?int $translationId): string
    {
        $base = Str::slug($value) ?: "wordpress-event-{$wordpressId}";
        $slug = $base;
        $suffix = 1;

        while (EventTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->when($translationId, fn ($query) => $query->whereKeyNot($translationId))
            ->exists()) {
            $slug = $base.'-wp-'.$wordpressId.($suffix > 1 ? "-{$suffix}" : '');
            $suffix++;
        }

        return $slug;
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function isFeatured(array $post): bool
    {
        return collect($post['event_categories'] ?? [])->contains(function ($category): bool {
            $value = is_array($category)
                ? ($category['slug'] ?? $category['name'] ?? '')
                : (string) $category;

            return str_contains(strtolower((string) $value), 'featured');
        });
    }

    /**
     * @param  array<string, mixed>  $post
     * @param  Collection<string, array<string, mixed>>  $postsByLocale
     */
    private function mediaFor(array $post, $postsByLocale): ?Media
    {
        $url = trim((string) ($post['featured_image_url'] ?? ''));

        if ($url === '' || parse_url($url, PHP_URL_SCHEME) !== 'https') {
            return null;
        }

        if (array_key_exists($url, $this->media)) {
            return $this->media[$url];
        }

        try {
            $disk = config('filesystems.default');
            $pathPrefix = 'media/wordpress/'.sha1($url).'.';
            $existing = Media::withTrashed()->where('path', 'like', $pathPrefix.'%')->first();

            if ($existing && Storage::disk($existing->disk)->exists($existing->path)) {
                if ($existing->trashed()) {
                    $existing->restore();
                }

                $existing->update([
                    'alt_text_al' => $this->plainText(data_get($postsByLocale->get('al'), 'title.rendered')),
                    'alt_text_en' => $this->plainText(data_get($postsByLocale->get('en'), 'title.rendered')),
                ]);

                return $this->media[$url] = $existing;
            }

            $response = Http::timeout(30)->retry(2, 250)->get($url)->throw();
            $contents = $response->body();

            if (strlen($contents) > 10 * 1024 * 1024 || ($dimensions = @getimagesizefromstring($contents)) === false) {
                throw new RuntimeException('The featured image is invalid or exceeds 10 MB.');
            }

            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
            $mime = $dimensions['mime'] ?? '';
            $extension = $extensions[$mime] ?? null;

            if ($extension === null) {
                throw new RuntimeException('The featured image type is not allowed.');
            }

            $path = $pathPrefix.$extension;

            if (! Storage::disk($disk)->exists($path) && ! Storage::disk($disk)->put($path, $contents)) {
                throw new RuntimeException('The featured image could not be stored.');
            }

            $media = Media::withTrashed()->firstOrNew(['path' => $path]);

            if ($media->trashed()) {
                $media->restore();
            }

            $media->fill([
                'disk' => $disk,
                'original_name' => Str::limit(urldecode(basename((string) parse_url($url, PHP_URL_PATH))), 255, ''),
                'mime_type' => $mime,
                'extension' => $extension,
                'size' => strlen($contents),
                'width' => $dimensions[0],
                'height' => $dimensions[1],
                'alt_text_al' => $this->plainText(data_get($postsByLocale->get('al'), 'title.rendered')),
                'alt_text_en' => $this->plainText(data_get($postsByLocale->get('en'), 'title.rendered')),
            ])->save();

            return $this->media[$url] = $media;
        } catch (Throwable $exception) {
            report($exception);
            $this->warnings[] = 'Featured image could not be imported: '.$exception->getMessage();

            return $this->media[$url] = null;
        }
    }
}
