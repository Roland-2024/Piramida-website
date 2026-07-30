<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_user_management(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_editor_cannot_access_user_management_or_create_users_directly(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->post(route('admin.users.store'), $this->validUserData())
            ->assertForbidden();
    }

    public function test_admin_can_create_an_editor(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), $this->validUserData())
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'editor@piramida.test',
            'role' => UserRole::Editor->value,
            'is_active' => true,
        ]);
    }

    public function test_user_creation_requires_a_unique_email_and_confirmed_password(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                ...$this->validUserData(),
                'email' => $existing->email,
                'password_confirmation' => 'different-password',
            ])
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_admin_can_deactivate_an_editor(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $editor), [
                'name' => $editor->name,
                'email' => $editor->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => UserRole::Editor->value,
                'is_active' => false,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($editor->fresh()->is_active);
    }

    public function test_final_active_admin_cannot_demote_or_deactivate_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => UserRole::Editor->value,
                'is_active' => false,
            ])
            ->assertSessionHasErrors('role');

        $admin->refresh();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->is_active);
    }

    /**
     * @return array<string, mixed>
     */
    private function validUserData(): array
    {
        return [
            'name' => 'Piramida Editor',
            'email' => 'editor@piramida.test',
            'password' => 'Secure-Password-2026',
            'password_confirmation' => 'Secure-Password-2026',
            'role' => UserRole::Editor->value,
            'is_active' => true,
        ];
    }
}
