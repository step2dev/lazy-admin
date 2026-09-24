<x-lazy-layout>
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ __('Roles') }}</h1>
                <p class="mt-1 text-sm opacity-70">{{ __('Manage admin roles and their permissions.') }}</p>
            </div>

            @can('role_create')
                <a class="btn btn-primary" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.role.create') }}">
                    {{ __('Create role') }}
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif

        <div class="overflow-x-auto rounded-box border border-base-300">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Permissions') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td class="font-medium">{{ $role->name }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($role->permissions->pluck('name') as $permission)
                                        <span class="badge badge-outline">{{ $permission }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    @can('role_edit')
                                        <a class="btn btn-sm" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.role.edit', $role) }}">
                                            {{ __('Edit') }}
                                        </a>
                                    @endcan

                                    @can('role_delete')
                                        @if ($role->name !== config('lazy.admin.permissions.super_admin_role', 'superadmin'))
                                            <form method="POST"
                                                  action="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.role.destroy', $role) }}"
                                                  onsubmit="return confirm('{{ __('Delete this role?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-error" type="submit">{{ __('Delete') }}</button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center opacity-70">{{ __('No roles found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-lazy-layout>
