<x-lazy-layout>
    @php
        $routePrefix = trim(config('lazy.admin.route.name', 'admin.'), '.');
        $superAdminRole = (string) config('lazy.admin.permissions.super_admin_role', 'superadmin');
        $enforcePermissions = (bool) config('lazy.admin.permissions.enforce', true);
        $user = auth()->user();

        $canViewRoles = ! $enforcePermissions || $user?->can('roles.view');
        $canCreateRoles = ! $enforcePermissions || $user?->can('roles.create');
        $canEditRoles = ! $enforcePermissions || $user?->can('roles.edit');
        $canDeleteRoles = ! $enforcePermissions || $user?->can('roles.delete');

        $canViewPermissions = ! $enforcePermissions || $user?->can('permissions.view');
        $canCreatePermissions = ! $enforcePermissions || $user?->can('permissions.create');
        $canEditPermissions = ! $enforcePermissions || $user?->can('permissions.edit');
        $canDeletePermissions = ! $enforcePermissions || $user?->can('permissions.delete');

        $selectedIsSuperAdmin = $selectedRole?->name === $superAdminRole;

        $permissionGroups = $permissions->groupBy(
            static fn ($permission) => str_contains($permission->name, '.')
                ? str($permission->name)->before('.')->headline()->toString()
                : __('Other')
        );
    @endphp

    <div class="mx-auto max-w-[1500px] space-y-6">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-semibold">{{ __('Access') }}</h1>
            <p class="text-sm opacity-60">
                {{ __('Manage admin roles and the permissions assigned to each role.') }}
            </p>
        </div>

        @if (session('status'))
            <div class="alert alert-success shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <div @class([
            'grid gap-8',
            'xl:grid-cols-[380px_minmax(0,1fr)]' => $canViewRoles,
            'grid-cols-1' => ! $canViewRoles,
        ])>
            @if ($canViewRoles)
                <aside class="space-y-6">
                    @if ($canCreateRoles)
                        <section class="space-y-3">
                            <div class="text-xs font-semibold uppercase tracking-[0.22em] opacity-50">
                                {{ __('Create role') }}
                            </div>

                            <form
                                method="POST"
                                action="{{ route($routePrefix.'.role.store') }}"
                                class="flex gap-3"
                            >
                                @csrf

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="input input-bordered min-w-0 flex-1"
                                    placeholder="support-moderator"
                                    required
                                />

                                <button type="submit" class="btn btn-primary shrink-0">
                                    <span class="text-lg leading-none">＋</span>
                                    {{ __('Create') }}
                                </button>
                            </form>
                        </section>
                    @endif

                    <section class="space-y-3">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] opacity-50">
                            {{ __('Roles') }}
                        </div>

                        <div class="space-y-3">
                            @forelse ($roles as $role)
                                @php
                                    $isActiveRole = $selectedRole
                                        && (string) $selectedRole->getRouteKey() === (string) $role->getRouteKey();
                                    $isSuperAdmin = $role->name === $superAdminRole;
                                @endphp

                                <a
                                    href="{{ route($routePrefix.'.access.index', ['role' => $role->getRouteKey()]) }}"
                                    @class([
                                        'block rounded-2xl border px-5 py-4 transition duration-150',
                                        'border-primary bg-base-100 shadow-sm ring-1 ring-primary/20' => $isActiveRole,
                                        'border-base-300 bg-base-200/40 hover:border-base-content/25 hover:bg-base-200/70' => ! $isActiveRole,
                                    ])
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="truncate text-lg font-semibold">
                                                {{ $role->name }}
                                            </div>

                                            <div class="mt-1 text-sm opacity-55">
                                                {{ __('guard: :guard', ['guard' => $role->guard_name]) }}
                                            </div>
                                        </div>

                                        @if ($isSuperAdmin)
                                            <span class="badge badge-warning badge-outline shrink-0">
                                                {{ __('locked') }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-2xl border border-dashed border-base-300 p-6 text-sm opacity-60">
                                    {{ __('No roles found.') }}
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-2xl border border-info/30 bg-info/15 p-5 text-info-content">
                        <div class="flex gap-3">
                            <div class="flex size-7 shrink-0 items-center justify-center rounded-full border border-current text-sm">
                                i
                            </div>

                            <div>
                                <div class="font-semibold">{{ __('Access rules') }}</div>
                                <p class="mt-2 text-sm leading-6 opacity-80">
                                    {{ __('Only :role permissions are locked. All other roles can be edited here.', ['role' => $superAdminRole]) }}
                                </p>
                            </div>
                        </div>
                    </section>
                </aside>
            @endif

            <main class="min-w-0 space-y-6">
                @if ($canViewRoles && $selectedRole)
                    <form
                        method="POST"
                        action="{{ route($routePrefix.'.role.update', $selectedRole) }}"
                        class="space-y-6"
                    >
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                            <div class="min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] opacity-50">
                                    {{ __('Permissions') }}
                                </div>

                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                    @if ($canEditRoles && ! $selectedIsSuperAdmin)
                                        <label class="form-control w-full max-w-sm">
                                            <span class="label-text mb-1 text-xs opacity-55">{{ __('Role name') }}</span>
                                            <input
                                                type="text"
                                                name="name"
                                                value="{{ old('name', $selectedRole->name) }}"
                                                class="input input-bordered input-sm text-lg font-semibold"
                                                required
                                            />
                                        </label>
                                    @else
                                        <h2 class="text-2xl font-semibold">{{ $selectedRole->name }}</h2>
                                        <input type="hidden" name="name" value="{{ $selectedRole->name }}" />
                                    @endif

                                    @if ($selectedIsSuperAdmin)
                                        <span class="badge badge-warning badge-outline">
                                            {{ __('locked') }}
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 text-sm opacity-60">
                                    @if ($selectedIsSuperAdmin)
                                        {{ __('This role is granted every permission automatically.') }}
                                    @else
                                        {{ __('Permissions are split into logical groups for the selected role.') }}
                                    @endif
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @if ($canDeleteRoles && ! $selectedIsSuperAdmin)
                                    <button
                                        type="submit"
                                        form="delete-selected-role"
                                        class="btn btn-ghost btn-sm text-error"
                                    >
                                        {{ __('Delete role') }}
                                    </button>
                                @endif

                                @if ($canEditRoles && ! $selectedIsSuperAdmin)
                                    <button type="submit" class="btn btn-primary min-w-28">
                                        ✓ {{ __('Save') }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-5">
                            @forelse ($permissionGroups as $group => $groupPermissions)
                                <fieldset class="rounded-3xl border border-base-300 bg-base-100/40 p-5">
                                    <div class="mb-5">
                                        <h3 class="text-lg font-semibold">{{ $group }}</h3>
                                        <div class="text-sm opacity-50">
                                            {{ trans_choice(':count permission|:count permissions', $groupPermissions->count(), ['count' => $groupPermissions->count()]) }}
                                        </div>
                                    </div>

                                    <div class="grid gap-3 md:grid-cols-2 2xl:grid-cols-3">
                                        @foreach ($groupPermissions as $permission)
                                            @php
                                                $permissionName = str_contains($permission->name, '.')
                                                    ? str($permission->name)->after('.')->replace('.', ' · ')->toString()
                                                    : $permission->name;
                                                $permissionChecked = $selectedIsSuperAdmin
                                                    || $selectedRole->hasPermissionTo($permission);
                                            @endphp

                                            <label @class([
                                                'flex min-h-20 items-center gap-4 rounded-2xl border p-4 transition',
                                                'cursor-pointer border-base-300 bg-base-200/20 hover:border-primary/40' => $canEditRoles && ! $selectedIsSuperAdmin,
                                                'cursor-default border-base-300 bg-base-200/10 opacity-75' => ! $canEditRoles || $selectedIsSuperAdmin,
                                            ])>
                                                <input
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->name }}"
                                                    class="checkbox checkbox-primary checkbox-sm"
                                                    @checked($permissionChecked)
                                                    @disabled(! $canEditRoles || $selectedIsSuperAdmin)
                                                />

                                                <span class="min-w-0">
                                                    <span class="block break-words font-medium">
                                                        {{ $permissionName }}
                                                    </span>
                                                    <span class="mt-1 block text-xs opacity-45">
                                                        {{ $permission->name }}
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @empty
                                <div class="rounded-3xl border border-dashed border-base-300 p-10 text-center opacity-60">
                                    {{ __('No permissions found.') }}
                                </div>
                            @endforelse
                        </div>
                    </form>

                    @if ($canDeleteRoles && ! $selectedIsSuperAdmin)
                        <form
                            id="delete-selected-role"
                            method="POST"
                            action="{{ route($routePrefix.'.role.destroy', $selectedRole) }}"
                            onsubmit="return confirm('{{ __('Delete this role?') }}')"
                        >
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                @endif

                @if ($canViewPermissions)
                    <details class="collapse collapse-arrow rounded-3xl border border-base-300 bg-base-100/40">
                        <summary class="collapse-title text-lg font-semibold">
                            {{ __('Permission catalog') }}
                            <span class="ml-2 text-sm font-normal opacity-50">
                                {{ __(':count total', ['count' => $permissions->count()]) }}
                            </span>
                        </summary>

                        <div class="collapse-content space-y-5">
                            <p class="text-sm opacity-60">
                                {{ __('Create, rename or remove reusable permissions. Role assignments are managed above.') }}
                            </p>

                            @if ($canCreatePermissions)
                                <form
                                    method="POST"
                                    action="{{ route($routePrefix.'.permission.store') }}"
                                    class="flex flex-col gap-3 sm:flex-row"
                                >
                                    @csrf

                                    <input
                                        type="text"
                                        name="name"
                                        class="input input-bordered min-w-0 flex-1"
                                        placeholder="reports.view"
                                        required
                                    />

                                    <button type="submit" class="btn btn-primary">
                                        ＋ {{ __('Create permission') }}
                                    </button>
                                </form>
                            @endif

                            <div class="grid gap-3 lg:grid-cols-2">
                                @foreach ($permissions as $permission)
                                    <div class="rounded-2xl border border-base-300 p-4">
                                        <form
                                            method="POST"
                                            action="{{ route($routePrefix.'.permission.update', $permission) }}"
                                            class="flex items-center gap-2"
                                        >
                                            @csrf
                                            @method('PUT')

                                            @if ($canEditPermissions)
                                                <input
                                                    type="text"
                                                    name="name"
                                                    value="{{ $permission->name }}"
                                                    class="input input-bordered input-sm min-w-0 flex-1"
                                                    required
                                                />

                                                <button type="submit" class="btn btn-sm">
                                                    {{ __('Save') }}
                                                </button>
                                            @else
                                                <span class="min-w-0 flex-1 break-all font-medium">
                                                    {{ $permission->name }}
                                                </span>
                                            @endif

                                            @if ($canDeletePermissions)
                                                <button
                                                    type="submit"
                                                    form="delete-permission-{{ $permission->getKey() }}"
                                                    class="btn btn-ghost btn-sm text-error"
                                                    title="{{ __('Delete') }}"
                                                >
                                                    ×
                                                </button>
                                            @endif
                                        </form>

                                        @if ($canDeletePermissions)
                                            <form
                                                id="delete-permission-{{ $permission->getKey() }}"
                                                method="POST"
                                                action="{{ route($routePrefix.'.permission.destroy', $permission) }}"
                                                onsubmit="return confirm('{{ __('Delete this permission?') }}')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </details>
                @endif
            </main>
        </div>
    </div>
</x-lazy-layout>
