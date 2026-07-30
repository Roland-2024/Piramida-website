<x-layouts.admin title="Users">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Dashboard users</h2>
            <p class="mt-1 text-sm text-slate-500">Manage Admin and Editor access.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Create user</a>
    </div>

    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-4">
        <div class="sm:col-span-2">
            <label for="search" class="sr-only">Search users</label>
            <input id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name or email" class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
        </div>
        <div>
            <label for="role-filter" class="sr-only">Role</label>
            <select id="role-filter" name="role" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
                <option value="">All roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected(($filters['role'] ?? '') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <label for="status-filter" class="sr-only">Status</label>
            <select id="status-filter" name="status" class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
                <option value="">All statuses</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
            </select>
            <button class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm font-medium hover:bg-slate-50" type="submit">Filter</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($users->isEmpty())
            <div class="px-6 py-14 text-center">
                <h3 class="font-semibold">No users found</h3>
                <p class="mt-1 text-sm text-slate-500">Adjust the filters or create a dashboard user.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Role</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Last login</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $account)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <p class="font-medium">{{ $account->name }}</p>
                                    <p class="text-slate-500">{{ $account->email }}</p>
                                </td>
                                <td class="px-5 py-4">{{ $account->role->label() }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $account->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500">{{ $account->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.users.edit', $account) }}" class="font-medium text-amber-700 hover:text-amber-800">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-5 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
