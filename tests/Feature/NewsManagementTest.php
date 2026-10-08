<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Attraction;
use App\Models\Business;
use App\Models\Career;
use App\Models\Event;
use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Program;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editors_can_trash_news_but_only_admins_can_restore_it(): void
    {
        $article = News::factory()->create();
        $editor = User::factory()->create();
        $this->actingAs($editor)->get(route('admin.news.index'))->assertOk()->assertSee('Move this article to trash?');
        $this->get(route('admin.news.show', $article))->assertOk()->assertSee('>Trash</button>', false);
        $this->delete(route('admin.news.destroy', $article))->assertRedirect();
        $this->assertSoftDeleted($article);
        $this->get(route('admin.news.index', ['trashed' => 'only']))->assertOk()->assertDontSee('>Restore</button>', false);
        $this->post(route('admin.news.restore', $article->id))->assertForbidden();
        $this->assertFalse($editor->can('forceDelete', $article));
        foreach ([Page::class, PageSection::class, Event::class,
            Program::class, Attraction::class, Business::class,
            Space::class, Career::class, Media::class] as $model) {
            $this->assertFalse($editor->can('delete', new $model));
        }
        $this->actingAs(User::factory()->admin()->create());
        $this->get(route('admin.news.index', ['trashed' => 'only']))->assertOk()->assertSee('Restore');
        $this->post(route('admin.news.restore', $article->id))->assertRedirect();
        $this->assertNotSoftDeleted($article);
        $this->delete(route('admin.news.destroy', $article))->assertRedirect();
        $this->assertSoftDeleted($article);
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
