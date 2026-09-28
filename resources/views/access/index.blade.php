<x-lazy-layout>
<div class="mx-auto max-w-[1500px] space-y-6">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-semibold">{{ __('Access') }}</h1>
            <p class="text-sm opacity-60">
                {{ __('Manage admin roles and the permissions assigned to each role.') }}
            </p>
        </div>

        @if (session('status'))
            <x-lazy-alert success class="shadow-sm" :message="session('status')" />
        @endif

        <div class="{{ $layoutClass }}">
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

                                <x-lazy-input
                                    type="text"
                                    name="name"
                                    :value="old('name')"
                                    class="min-w-0 flex-1"
                                    placeholder="support-moderator"
                                    required
                                />

                                <x-lazy-btn primary type="submit" class="shrink-0">
                                    <span class="text-lg leading-none">＋</span>
                                    {{ __('Create') }}
                                </x-lazy-btn>
                            </form>
                        </section>
                    @endif

                    <section class="space-y-3">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] opacity-50">
                            {{ __('Roles') }}
                        </div>

                        <div class="space-y-3">
                            @forelse ($roleItems as $roleItem)
                                <a
                                    href="{{ $roleItem['href'] }}"
                                    class="{{ $roleItem['classes'] }}"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="truncate text-lg font-semibold">
                                                {{ $roleItem['role']->name }}
                                            </div>

                                            <div class="mt-1 text-sm opacity-55">
                                                {{ __('guard: :guard', ['guard' => $roleItem['role']->guard_name]) }}
                                            </div>
                                        </div>

                                        @if ($roleItem['superAdmin'])
                                            <x-lazy-badge warning outline class="shrink-0" :label="__('locked')" />
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
                                            <x-lazy-input
                                                type="text"
                                                name="name"
                                                :value="old('name', $selectedRole->name)"
                                                sm
                                                class="text-lg font-semibold"
                                                required
                                            />
                                        </label>
                                    @else
                                        <h2 class="text-2xl font-semibold">{{ $selectedRole->name }}</h2>
                                        <input type="hidden" name="name" value="{{ $selectedRole->name }}" />
                                    @endif

                                    @if ($selectedIsSuperAdmin)
                                        <x-lazy-badge warning outline :label="__('locked')" />
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
                                    <x-lazy-btn
                                        ghost
                                        sm
                                        type="submit"
                                        form="delete-selected-role"
                                        class="text-error"
                                        :label="__('Delete role')"
                                    />
                                @endif

                                @if ($canEditRoles && ! $selectedIsSuperAdmin)
                                    <x-lazy-btn primary type="submit" class="min-w-28">
                                        ✓ {{ __('Save') }}
                                    </x-lazy-btn>
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
                                        @foreach ($groupPermissions as $permissionItem)
                                            <label class="{{ $permissionItem['classes'] }}">
                                                <x-lazy-checkbox
                                                    primary
                                                    sm
                                                    name="permissions[]"
                                                    :value="$permissionItem['permission']->name"
                                                    :checked="$permissionItem['checked']"
                                                    :disabled="! $permissionsEditable"
                                                />

                                                <span class="min-w-0">
                                                    <span class="block break-words font-medium">
                                                        {{ $permissionItem['label'] }}
                                                    </span>
                                                    <span class="mt-1 block text-xs opacity-45">
                                                        {{ $permissionItem['permission']->name }}
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
                    <x-lazy-collapse
                        class="rounded-3xl border border-base-300 bg-base-100/40"
                        summary-class="text-lg font-semibold"
                        content-class="space-y-5"
                    >
                        <x-slot:summary>
                            {{ __('Permission catalog') }}
                            <span class="ml-2 text-sm font-normal opacity-50">
                                {{ __(':count total', ['count' => $permissions->count()]) }}
                            </span>
                        </x-slot:summary>
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

                                    <x-lazy-input
                                        type="text"
                                        name="name"
                                        class="min-w-0 flex-1"
                                        placeholder="reports.view"
                                        required
                                    />

                                    <x-lazy-btn primary type="submit">
                                        ＋ {{ __('Create permission') }}
                                    </x-lazy-btn>
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
                                                <x-lazy-input
                                                    type="text"
                                                    name="name"
                                                    :value="$permissionItem['permission']->name"
                                                    sm
                                                    class="min-w-0 flex-1"
                                                    required
                                                />

                                                <x-lazy-btn sm type="submit" :label="__('Save')" />
                                            @else
                                                <span class="min-w-0 flex-1 break-all font-medium">
                                                    {{ $permissionItem['permission']->name }}
                                                </span>
                                            @endif

                                            @if ($canDeletePermissions)
                                                <x-lazy-btn
                                                    ghost
                                                    sm
                                                    type="submit"
                                                    form="delete-permission-{{ $permission->getKey() }}"
                                                    class="text-error"
                                                    title="{{ __('Delete') }}"
                                                >×</x-lazy-btn>
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
                    </x-lazy-collapse>
                @endif
            </main>
        </div>
    </div>
</x-lazy-layout>
