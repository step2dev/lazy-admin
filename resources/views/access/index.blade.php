<x-lazy-layout>
<div class="mx-auto w-full max-w-[1600px] space-y-6 px-1 pb-8">
        <div class="flex flex-col gap-1 border-b border-base-300 pb-5">
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('Access') }}</h1>
            <p class="text-sm opacity-60">
                {{ __('Manage admin roles and the permissions assigned to each role.') }}
            </p>
        </div>

        @if (session('status'))
            <x-lazy-alert success class="shadow-sm" :message="session('status')" />
        @endif

        <div class="grid grid-cols-1 items-start gap-6 {{ $canViewRoles ? 'xl:grid-cols-[300px_minmax(0,1fr)]' : '' }}">
            @if ($canViewRoles)
                <aside class="min-w-0 space-y-4 xl:sticky xl:top-20 xl:max-h-[calc(100vh-6rem)] xl:overflow-y-auto xl:pr-1">
                    @if ($canCreateRoles)
                        <section class="rounded-xl border border-base-300 bg-base-100 p-4 space-y-3">
                            <div class="text-xs font-semibold uppercase tracking-wider opacity-60">
                                {{ __('Create role') }}
                            </div>

                            <form
                                method="POST"
                                action="{{ route($routePrefix.'.role.store') }}"
                                class="flex flex-col gap-2 sm:flex-row xl:flex-col"
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

                    <section class="rounded-xl border border-base-300 bg-base-100 p-3 space-y-3">
                        <div class="flex items-center justify-between px-2 text-xs font-semibold uppercase tracking-wider opacity-60">
                            {{ __('Roles') }}
                            <span class="text-xs font-normal tabular-nums">{{ $roles->count() }}</span>
                        </div>

                        <nav class="max-h-[45vh] space-y-1 overflow-y-auto xl:max-h-none" aria-label="{{ __('Roles') }}">
                            @forelse ($roleItems as $roleItem)
                                <a
                                    href="{{ $roleItem['href'] }}"
                                    class="block rounded-lg border px-3 py-2.5 transition-colors {{ $roleItem['active'] ? 'border-primary/50 bg-primary/10 text-base-content' : 'border-transparent hover:border-base-300 hover:bg-base-200/70' }}"
                                    @if ($roleItem['active']) aria-current="page" @endif
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold">
                                                {{ $roleItem['role']->name }}
                                            </div>

                                            <div class="mt-0.5 text-xs opacity-55">
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
                        </nav>
                    </section>

                    <section class="rounded-xl border border-info/20 bg-info/5 p-4">
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

            <main class="min-w-0 space-y-5">
                @if ($canViewRoles && $selectedRole)
                    <form
                        method="POST"
                        action="{{ route($routePrefix.'.role.update', $selectedRole) }}"
                        class="min-w-0 space-y-5 rounded-xl border border-base-300 bg-base-100 p-4 shadow-sm sm:p-6"
                    >
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col gap-4 border-b border-base-300 pb-5 lg:flex-row lg:items-end lg:justify-between">
                            <div class="min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-wider opacity-60">
                                    {{ __('Permissions') }}
                                </div>

                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                    @if ($canEditRoles && ! $selectedIsSuperAdmin)
                                        <label class="w-full max-w-sm">
                                            <span class="mb-1 block text-xs font-medium opacity-55">{{ __('Role name') }}</span>
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

                        <div class="space-y-4">
                            @forelse ($permissionGroups as $group => $groupPermissions)
                                <x-lazy-fieldset class="rounded-xl border border-base-300 bg-base-200/20 p-4 sm:p-5">
                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold">{{ $group }}</h3>
                                        <div class="text-sm opacity-50">
                                            {{ trans_choice(':count permission|:count permissions', count($groupPermissions), ['count' => count($groupPermissions)]) }}
                                        </div>
                                    </div>

                                    <div class="grid gap-2 md:grid-cols-2 2xl:grid-cols-3">
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
                                </x-lazy-fieldset>
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
                        class="rounded-xl border border-base-300 bg-base-100 shadow-sm"
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
                                    <x-lazy-card compact class="border border-base-300 bg-base-100 shadow-sm">
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
                                                    :value="$permission->name"
                                                    sm
                                                    class="min-w-0 flex-1"
                                                    required
                                                />

                                                <x-lazy-btn sm type="submit" :label="__('Save')" />
                                            @else
                                                <span class="min-w-0 flex-1 break-all font-medium">
                                                    {{ $permission->name }}
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
                                    </x-lazy-card>
                                @endforeach
                            </div>
                    </x-lazy-collapse>
                @endif
            </main>
        </div>
    </div>
</x-lazy-layout>
