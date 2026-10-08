<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Media;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_list_trash_action_is_admin_only_and_reversible(): void
    {
        $article = News::factory()->create();
        $this->actingAs(User::factory()->create())->get(route('admin.news.index'))->assertOk()->assertDontSee('data-confirm=', false);
        $this->delete(route('admin.news.destroy', $article))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get(route('admin.news.index'))->assertOk()->assertSee('Move this article to trash?');
        $this->delete(route('admin.news.destroy', $article))->assertRedirect();
        $this->assertSoftDeleted($article);
        $this->get(route('admin.news.index', ['trashed' => 'only']))->assertOk()->assertSee('Restore');
        $this->post(route('admin.news.restore', $article->id))->assertRedirect();
        $this->assertNotSoftDeleted($article);
    }

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
