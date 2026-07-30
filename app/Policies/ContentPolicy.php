<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function view(User $user, Model $content): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Model $content): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Model $content): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Model $content): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Model $content): bool
    {
        return $user->isAdmin();
    }
}
