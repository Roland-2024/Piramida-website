<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class InitialAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = (string) config('cms.initial_admin.name');
        $email = (string) config('cms.initial_admin.email');
        $password = (string) config('cms.initial_admin.password');

        if ($name === '' || $email === '' || $password === '') {
            $this->command?->warn('Initial Admin skipped: set INITIAL_ADMIN_NAME, INITIAL_ADMIN_EMAIL, and INITIAL_ADMIN_PASSWORD.');

            return;
        }

        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => UserRole::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
