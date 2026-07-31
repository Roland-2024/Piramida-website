<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Media;
use App\Models\News;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_scope_excludes_drafts_and_future_dated_news(): void
    {
        $visible = News::factory()->published()->create();
        News::factory()->create();
        News::factory()->create([
            'status' => ContentStatus::Published,
            'published_at' => now()->addDay(),
        ]);

        $this->assertSame([$visible->id], News::query()->published()->pluck('id')->all());
    }

    public function test_news_slug_is_unique_per_locale(): void
    {
        $first = News::factory()->create();

        $this->expectException(QueryException::class);

        $second = News::factory()->create();
        $second->translations()->where('locale', 'al')->update([
            'slug' => $first->translation('al', false)?->slug,
        ]);
    }

    public function test_news_gallery_keeps_the_selected_design_order(): void
    {
        $article = News::factory()->create();
        $first = Media::factory()->create();
        $second = Media::factory()->create();

        $article->syncGallery([$second->id, $first->id]);

        $this->assertSame([$second->id, $first->id], $article->gallery()->pluck('media.id')->all());
    }
}
