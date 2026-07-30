<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::enum(UserRole::class)],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $users = User::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, fn (Builder $query, string $role) => $query->where('role', $role))
            ->when(
                $filters['status'] ?? null,
                fn (Builder $query, string $status) => $query->where('is_active', $status === 'active')
            )
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $filters,
            'roles' => UserRole::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['roles' => UserRole::cases()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Dashboard user created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'account' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        DB::transaction(function () use ($data, $request, $user): void {
            $account = User::query()->lockForUpdate()->findOrFail($user->id);
            $newRole = UserRole::from($data['role']);
            $newIsActive = (bool) $data['is_active'];

            if ($request->user()->is($account) && ($newRole !== $account->role || $newIsActive !== $account->is_active)) {
                throw ValidationException::withMessages([
                    'role' => 'You cannot change your own role or active status.',
                ]);
            }

            $removesActiveAdmin = $account->isAdmin()
                && $account->is_active
                && ($newRole !== UserRole::Admin || ! $newIsActive);

            if ($removesActiveAdmin) {
                $activeAdminIds = User::query()
                    ->where('role', UserRole::Admin)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->pluck('id');

                if ($activeAdminIds->count() === 1) {
                    throw ValidationException::withMessages([
                        'role' => 'The final active Admin cannot be demoted or deactivated.',
                    ]);
                }
            }

            $account->update($data);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Dashboard user updated.');
    }
}
