<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\SectionType;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = User::query()->where('role', 'admin')->value('id');

        $page = Page::query()
            ->whereHas('translations', fn ($query) => $query->where('locale', 'al')->where('slug', 'kryefaqja'))
            ->first();

        if (! $page) {
            $page = Page::query()->create([
                'is_homepage' => true,
                'status' => ContentStatus::Published,
                'published_at' => now()->subDay(),
                'display_order' => 0,
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ]);
            $page->syncTranslations([
                'al' => [
                    'title' => 'Mirë se vini në Piramida',
                    'slug' => 'kryefaqja',
                    'short_description' => 'Një hapësirë për kulturë, ide dhe komunitet.',
                    'content' => '<p>Kjo është faqja e përkohshme për testimin e përmbajtjes dinamike.</p>',
                    'seo_title' => 'Piramida',
                    'seo_description' => 'Kulturë, ide dhe komunitet në Tiranë.',
                ],
                'en' => [
                    'title' => 'Welcome to Piramida',
                    'slug' => 'home',
                    'short_description' => 'A space for culture, ideas, and community.',
                    'content' => '<p>This temporary page demonstrates dynamic managed content.</p>',
                    'seo_title' => 'Piramida',
                    'seo_description' => 'Culture, ideas, and community in Tirana.',
                ],
            ]);
        }

        if (! $page->sections()->where('internal_name', 'Demo introduction')->exists()) {
            $section = PageSection::query()->create([
                'page_id' => $page->id,
                'internal_name' => 'Demo introduction',
                'type' => SectionType::TextImage,
                'display_order' => 10,
                'is_active' => true,
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ]);
            $section->syncTranslations([
                'al' => [
                    'title' => 'Një pikë takimi për qytetin',
                    'description' => '<p>Përmbajtja mund të menaxhohet në shqip dhe anglisht nga paneli i administrimit.</p>',
                ],
                'en' => [
                    'title' => 'A meeting point for the city',
                    'description' => '<p>Content can be managed in Albanian and English from the administration dashboard.</p>',
                ],
            ]);
        }

        $this->seedNews($ownerId);
        $this->seedEvent($ownerId);
    }

    private function seedNews(?int $ownerId): void
    {
        if (News::query()->whereHas('translations', fn ($query) => $query->where('slug', 'lajmi-demo'))->exists()) {
            return;
        }

        $news = News::query()->create([
            'status' => ContentStatus::Published,
            'published_at' => now()->subHours(2),
            'author_name' => 'Piramida',
            'created_by' => $ownerId,
            'updated_by' => $ownerId,
        ]);
        $news->syncTranslations([
            'al' => [
                'title' => 'Lajm demonstrues',
                'slug' => 'lajmi-demo',
                'excerpt' => 'Një artikull shembull për mjedisin lokal.',
                'content' => '<p>Ky artikull mund të zëvendësohet nga paneli.</p>',
            ],
            'en' => [
                'title' => 'Demonstration news',
                'slug' => 'demo-news',
                'excerpt' => 'An example article for the local environment.',
                'content' => '<p>This article can be replaced from the dashboard.</p>',
            ],
        ]);
    }

    private function seedEvent(?int $ownerId): void
    {
        if (Event::query()->whereHas('translations', fn ($query) => $query->where('slug', 'eventi-demo'))->exists()) {
            return;
        }

        $event = Event::query()->create([
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
            'starts_at' => now()->addWeek()->setTime(18, 0),
            'ends_at' => now()->addWeek()->setTime(20, 0),
            'created_by' => $ownerId,
            'updated_by' => $ownerId,
        ]);
        $event->syncTranslations([
            'al' => [
                'title' => 'Event demonstrues',
                'slug' => 'eventi-demo',
                'short_description' => 'Një event shembull për mjedisin lokal.',
                'description' => '<p>Datat dhe përmbajtja menaxhohen nga paneli.</p>',
                'location' => 'Piramida, Tiranë',
            ],
            'en' => [
                'title' => 'Demonstration event',
                'slug' => 'demo-event',
                'short_description' => 'An example event for the local environment.',
                'description' => '<p>Dates and content are managed from the dashboard.</p>',
                'location' => 'Piramida, Tirana',
            ],
        ]);
    }
}
