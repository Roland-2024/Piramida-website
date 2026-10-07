<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Workspace overview')
            ->assertSee('View website')
            ->assertSee('admin-navigation');
    }

    public function test_editor_can_access_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Create article')
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('admin.settings.edit'), false);
    }

    public function test_inactive_authenticated_user_is_logged_out(): void
    {
        $user = User::factory()->inactive()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
