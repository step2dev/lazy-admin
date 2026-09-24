<x-lazy-layout>
    @php
        $routePrefix = trim(config('lazy.admin.route.name', 'admin.'), '.');
        $superAdminRole = config('lazy.admin.permissions.super_admin_role', 'superadmin');
        $canViewRoles = ! config('lazy.admin.permissions.enforce', true) || auth()->user()?->can('roles.view');
        $canViewPermissions = ! config('lazy.admin.permissions.enforce', true) || auth()->user()?->can('permissions.view');

        $permissionGroups = $permissions->groupBy(
            static fn ($permission) => str_contains($permission->name, '.')
                ? str($permission->name)->before('.')->toString()
                : __('Other')
        );
    @endphp

    <div class="mx-auto max-w-7xl space-y-8">
        <div>
            <h1 class="text-2xl font-semibold">{{ __('Access') }}</h1>
            <p class="mt-1 text-sm opacity-70">
                {{ __('Roles control access to the admin panel. Permissions control what users can do inside it.') }}
            </p>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div @class([
            'grid gap-8',
            'xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]' => $canViewRoles && $canViewPermissions,
            'grid-cols-1' => ! ($canViewRoles && $canViewPermissions),
        ])>
            @if ($canViewRoles)
            <section class="space-y-5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ __('Roles') }}</h2>
                        <p class="text-sm opacity-70">{{ __('Assign permissions to each admin role.') }}</p>
                    </div>
                </div>

                @can('roles.create')
                    <form method="POST"
                          action="{{ route($routePrefix.'.role.store') }}"
                          class="rounded-box border border-base-300 bg-base-100 p-5">
                        @csrf

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <label class="form-control flex-1">
                                <span class="label-text">{{ __('New role') }}</span>
                                <input
                                    type="text"
                                    name="name"
                                    class="input input-bordered"
                                    placeholder="support"
                                    required
                                />
                            </label>

                            <button type="submit" class="btn btn-primary">
                                {{ __('Create role') }}
                            </button>
                        </div>
                    </form>
                @endcan

                <div class="space-y-4">
                    @forelse ($roles as $role)
                        @php($isSuperAdmin = $role->name === $superAdminRole)

                        <form method="POST"
                              action="{{ route($routePrefix.'.role.update', $role) }}"
                              class="rounded-box border border-base-300 bg-base-100 p-5">
                            @csrf
                            @method('PUT')

                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @can('roles.edit')
                                            <input
                                                type="text"
                                                name="name"
                                                value="{{ $role->name }}"
                                                class="input input-bordered input-sm max-w-xs font-semibold"
                                                @readonly($isSuperAdmin)
                                                required
                                            />
                                        @else
                                            <h3 class="font-semibold">{{ $role->name }}</h3>
                                        @endcan

                                        @if ($isSuperAdmin)
                                            <span class="badge badge-primary">{{ __('All permissions') }}</span>
                                        @endif
                                    </div>

                                    <p class="mt-2 text-sm opacity-60">
                                        {{ trans_choice(':count permission|:count permissions', $role->permissions->count(), ['count' => $role->permissions->count()]) }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @can('roles.edit')
                                        <button type="submit" class="btn btn-sm btn-primary" @disabled($isSuperAdmin)>
                                            {{ __('Save') }}
                                        </button>
                                    @endcan

                                    @can('roles.delete')
                                        @unless ($isSuperAdmin)
                                            <button
                                                type="submit"
                                                form="delete-role-{{ $role->getKey() }}"
                                                class="btn btn-sm btn-error"
                                            >
                                                {{ __('Delete') }}
                                            </button>
                                        @endunless
                                    @endcan
                                </div>
                            </div>

                            @unless ($isSuperAdmin)
                                <div class="mt-5 space-y-5">
                                    @forelse ($permissionGroups as $group => $groupPermissions)
                                        <fieldset>
                                            <legend class="mb-2 text-sm font-semibold uppercase tracking-wide opacity-60">
                                                {{ $group }}
                                            </legend>

                                            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                                @foreach ($groupPermissions as $permission)
                                                    <label class="flex items-center gap-2 rounded-box border border-base-300 p-3">
                                                        <input
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $permission->name }}"
                                                            class="checkbox checkbox-primary checkbox-sm"
                                                            @checked($role->hasPermissionTo($permission))
                                                            @disabled(! auth()->user()?->can('roles.edit'))
                                                        />
                                                        <span class="text-sm">{{ $permission->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </fieldset>
                                    @empty
                                        <p class="text-sm opacity-70">{{ __('No permissions found.') }}</p>
                                    @endforelse
                                </div>
                            @endunless
                        </form>

                        @can('roles.delete')
                            @unless ($isSuperAdmin)
                                <form
                                    id="delete-role-{{ $role->getKey() }}"
                                    method="POST"
                                    action="{{ route($routePrefix.'.role.destroy', $role) }}"
                                    onsubmit="return confirm('{{ __('Delete this role?') }}')"
                                >
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endunless
                        @endcan
                    @empty
                        <div class="rounded-box border border-dashed border-base-300 p-8 text-center opacity-70">
                            {{ __('No roles found.') }}
                        </div>
                    @endforelse
                </div>
            </section>
            @endif

            @if ($canViewPermissions)
            <aside class="space-y-5">
                <div>
                    <h2 class="text-xl font-semibold">{{ __('Permissions') }}</h2>
                    <p class="text-sm opacity-70">{{ __('Permissions can be shared by Lazy Admin modules.') }}</p>
                </div>

                @can('permissions.create')
                    <form method="POST"
                          action="{{ route($routePrefix.'.permission.store') }}"
                          class="rounded-box border border-base-300 bg-base-100 p-5">
                        @csrf

                        <label class="form-control">
                            <span class="label-text">{{ __('New permission') }}</span>
                            <input
                                type="text"
                                name="name"
                                class="input input-bordered"
                                placeholder="reports.view"
                                required
                            />
                        </label>

                        <button type="submit" class="btn btn-primary mt-3 w-full">
                            {{ __('Create permission') }}
                        </button>
                    </form>
                @endcan

                <div class="space-y-3">
                    @forelse ($permissions as $permission)
                        <div class="rounded-box border border-base-300 bg-base-100 p-4">
                            <form method="POST"
                                  action="{{ route($routePrefix.'.permission.update', $permission) }}"
                                  class="space-y-3">
                                @csrf
                                @method('PUT')

                                @can('permissions.edit')
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ $permission->name }}"
                                        class="input input-bordered input-sm w-full"
                                        required
                                    />
                                @else
                                    <div class="font-medium">{{ $permission->name }}</div>
                                @endcan

                                <div class="flex flex-wrap gap-1">
                                    @foreach ($permission->roles->pluck('name') as $roleName)
                                        <span class="badge badge-outline badge-sm">{{ $roleName }}</span>
                                    @endforeach
                                </div>

                                <div class="flex justify-end gap-2">
                                    @can('permissions.edit')
                                        <button type="submit" class="btn btn-sm">
                                            {{ __('Save') }}
                                        </button>
                                    @endcan

                                    @can('permissions.delete')
                                        <button
                                            type="submit"
                                            form="delete-permission-{{ $permission->getKey() }}"
                                            class="btn btn-sm btn-error"
                                        >
                                            {{ __('Delete') }}
                                        </button>
                                    @endcan
                                </div>
                            </form>

                            @can('permissions.delete')
                                <form
                                    id="delete-permission-{{ $permission->getKey() }}"
                                    method="POST"
                                    action="{{ route($routePrefix.'.permission.destroy', $permission) }}"
                                    onsubmit="return confirm('{{ __('Delete this permission?') }}')"
                                >
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endcan
                        </div>
                    @empty
                        <div class="rounded-box border border-dashed border-base-300 p-6 text-center opacity-70">
                            {{ __('No permissions found.') }}
                        </div>
                    @endforelse
                </div>
            </aside>
            @endif
        </div>
    </div>
</x-lazy-layout>
