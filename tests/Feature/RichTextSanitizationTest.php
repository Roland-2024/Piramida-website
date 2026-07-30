<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RichTextSanitizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rich_text_removes_scripts_event_handlers_and_unsafe_links(): void
    {
        $page = Page::factory()->create();
        $translation = $page->translation('en', false);

        $translation->update([
            'content' => '<p onclick="alert(1)">Safe <strong>text</strong><script>alert(2)</script><a href="javascript:alert(3)">link</a></p>',
        ]);

        $content = $translation->fresh()->content;

        $this->assertStringContainsString('<p>Safe <strong>text</strong><a>link</a></p>', $content);
        $this->assertStringNotContainsString('script', $content);
        $this->assertStringNotContainsString('onclick', $content);
        $this->assertStringNotContainsString('javascript:', $content);
    }

    public function test_rich_text_keeps_supported_formatting_and_relative_links(): void
    {
        $page = Page::factory()->create();
        $translation = $page->translation('en', false);

        $translation->update([
            'content' => '<h2>Heading</h2><ul><li>Item</li></ul><p><a href="/en/news">News</a></p>',
        ]);

        $content = $translation->fresh()->content;

        $this->assertStringContainsString('<h2>Heading</h2>', $content);
        $this->assertStringContainsString('<ul><li>Item</li></ul>', $content);
        $this->assertStringContainsString('href="/en/news"', $content);
    }
}
