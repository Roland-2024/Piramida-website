<?php

namespace Tests\Feature;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\ProgramCategory;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_and_edit_a_bilingual_program(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.programs.store'), [
                'category' => ProgramCategory::Education->value,
                'status' => ContentStatus::Published->value,
                'published_at' => now()->subMinute()->format('Y-m-d H:i:s'),
                'starts_at' => null,
                'ends_at' => null,
                'booking_mode' => BookingMode::Internal->value,
                'external_url' => null,
                'is_featured' => true,
                'display_order' => 4,
                'featured_media_id' => null,
                'gallery_media_ids' => [],
                'translations' => [
                    'al' => [
                        'title' => 'Laboratori i kodimit',
                        'slug' => '',
                        'description' => '<p>Program edukimi.</p>',
                    ],
                    'en' => [
                        'title' => 'Coding laboratory',
                        'slug' => '',
                        'description' => '<p>Education program.</p>',
                    ],
                ],
            ])
            ->assertRedirect();

        $program = Program::query()->firstOrFail();

        $this->assertSame('laboratori-i-kodimit', $program->translation('al', false)?->slug);
        $this->assertSame('coding-laboratory', $program->translation('en', false)?->slug);
        $this->assertSame($editor->id, $program->created_by);

        $this->actingAs($editor)
            ->get(route('admin.programs.edit', $program))
            ->assertOk()
            ->assertSee('Coding laboratory');
    }

    public function test_editor_cannot_delete_program_but_admin_can_restore_it(): void
    {
        $program = Program::factory()->create();
        $editor = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($editor)
            ->delete(route('admin.programs.destroy', $program))
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.programs.destroy', $program))
            ->assertRedirect(route('admin.programs.index'));

        $this->actingAs($admin)
            ->post(route('admin.programs.restore', ['id' => $program->id]))
            ->assertRedirect(route('admin.programs.index'));

        $this->assertNotSoftDeleted($program);
    }

    public function test_public_program_pages_hide_drafts_and_use_localized_slugs(): void
    {
        $published = Program::factory()->published()->create([
            'booking_mode' => BookingMode::Internal,
        ]);
        $draft = Program::factory()->create();

        $this->get(route('public.programs.index', 'en'))
            ->assertOk()
            ->assertSee("Program {$published->id}")
            ->assertDontSee("Program {$draft->id}");

        $this->get(route('public.programs.show', ['en', "program-{$published->id}"]))
            ->assertOk()
            ->assertSee(__('cms.request_confirmation_notice'));

        $this->get(route('public.programs.show', ['en', "programi-{$published->id}"]))
            ->assertNotFound();
    }
}
