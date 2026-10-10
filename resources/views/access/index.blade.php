<x-lazy-layout :title="__('lazy-admin::access.title')">
    <div class="mx-auto max-w-[1440px] space-y-5">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-semibold">{{ __('lazy-admin::access.title') }}</h1>
            <p class="text-sm opacity-60">
                {{ __('lazy-admin::access.description') }}
            </p>
        </div>

        @if ($errors->any())
            <x-lazy-alert error>
                <ul class="list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-lazy-alert>
        @endif

        @if (session('status'))
            <x-lazy-alert success class="shadow-sm" :message="session('status')" />
        @endif

        <div class="{{ $canViewRoles ? 'grid gap-5 lg:grid-cols-[260px_minmax(0,1fr)]' : 'grid gap-5' }}">
            @if ($canViewRoles)
                <aside class="space-y-4 lg:sticky lg:max-h-[calc(100dvh-6rem)] lg:overflow-y-auto lg:self-start" style="top: calc(var(--lazy-admin-content-sticky-offset, 4rem) + 1rem);">
                    @if ($canCreateRoles)
                        <section class="rounded-xl border border-base-300 bg-base-100 p-4 space-y-3">
                            <div class="text-xs font-semibold uppercase tracking-[0.22em] opacity-50">
                                {{ __('lazy-admin::access.create_role') }}
                            </div>

                            <form
                                method="POST"
                                action="{{ route($routePrefix.'.role.store') }}"
                                class="flex flex-col gap-2"
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
                                    {{ __('lazy-admin::access.create') }}
                                </x-lazy-btn>
                            </form>
                        </section>
                    @endif

                    <section class="space-y-3">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] opacity-50">
                            {{ __('lazy-admin::access.roles') }}
                        </div>

                        <div class="space-y-3">
                            @forelse ($roleItems as $roleItem)
                                <a
                                    href="{{ $roleItem['href'] }}"
                                    class="block rounded-xl border px-4 py-3 transition {{ $roleItem['active'] ? 'border-primary bg-primary/10' : 'border-base-300 bg-base-100 hover:bg-base-200' }}"
                                    @if ($roleItem['active']) aria-current="page" @endif
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold">
                                                {{ $roleItem['role']->name }}
                                            </div>

                                            <div class="mt-1 text-xs opacity-55">
                                                {{ __('lazy-admin::access.guard', ['guard' => $roleItem['role']->guard_name]) }}
                                            </div>
                                        </div>

                                        @if ($roleItem['superAdmin'])
                                            <x-lazy-badge warning outline class="shrink-0" :label="__('lazy-admin::access.locked')" />
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-2xl border border-dashed border-base-300 p-6 text-sm opacity-60">
                                    {{ __('lazy-admin::access.no_roles') }}
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-xl border border-info/30 bg-info/10 p-4 text-base-content">
                        <div class="flex gap-3">
                            <div class="flex size-7 shrink-0 items-center justify-center rounded-full border border-current text-sm">
                                i
                            </div>

                            <div>
                                <div class="font-semibold">{{ __('lazy-admin::access.rules') }}</div>
                                <p class="mt-2 text-sm leading-6 opacity-80">
                                    {{ __('lazy-admin::access.rules_description', ['role' => $superAdminRole]) }}
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
                        class="space-y-4"
                        x-data="{
                            query: '',
                            selected: 0,
                            matches(value) { return value.toLocaleLowerCase().includes(this.query.trim().toLocaleLowerCase()) },
                            recount() { this.selected = this.$el.querySelectorAll('input[name=\'permissions[]\']:checked').length },
                            init() { this.recount() },
                            toggleGroup(root, checked) {
                                root.querySelectorAll('[data-permission]').forEach(row => {
                                    const input = row.querySelector('input[type=checkbox]');
                                    if (input &amp;&amp; !input.disabled &amp;&amp; this.matches(row.dataset.search)) input.checked = checked;
                                });
                                this.recount();
                            }
                        }"
                        @change="recount()"
                    >
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                            <div class="min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] opacity-50">
                                    {{ __('lazy-admin::access.permissions') }}
                                </div>

                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                    @if ($canEditRoles && ! $selectedIsSuperAdmin)
                                        <label class="w-full max-w-sm">
                                            <span class="mb-1 block text-xs font-medium opacity-55">{{ __('lazy-admin::access.role_name') }}</span>
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
                                        <x-lazy-badge warning outline :label="__('lazy-admin::access.locked')" />
                                    @endif
                                </div>

                                <p class="mt-2 text-sm opacity-60">
                                    @if ($selectedIsSuperAdmin)
                                        {{ __('lazy-admin::access.superadmin_description') }}
                                    @else
                                        {{ __('lazy-admin::access.groups_description') }}
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
                                        :label="__('lazy-admin::access.delete_role')"
                                    />
                                @endif

                                @if ($canEditRoles && ! $selectedIsSuperAdmin)
                                    <x-lazy-btn primary type="submit" class="min-w-28">
                                        ✓ {{ __('lazy-admin::access.save') }}
                                    </x-lazy-btn>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-xl border border-base-300 bg-base-100 p-4">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium">{{ __('lazy-admin::access.search') }}</span>
                                <x-lazy-input type="search" x-model.debounce.150ms="query" placeholder="users.view" />
                            </label>
                            <p class="mt-2 text-xs opacity-60">{{ __('lazy-admin::access.filter_hint') }}</p>
                        </div>

                        <div class="space-y-4">
                            @forelse ($permissionGroups as $group => $groupPermissions)
                                <x-lazy-fieldset
                                    class="rounded-xl border border-base-300 bg-base-100 p-4"
                                    data-search="{{ $group.' '.collect($groupPermissions)->pluck('permission.name')->implode(' ') }}"
                                    x-show="matches($el.dataset.search)"
                                >
                                    <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                        <h3 class="text-sm font-semibold">{{ $group }}</h3>
                                        <div class="text-sm opacity-50">
                                            {{ trans_choice('lazy-admin::access.count', count($groupPermissions), ['count' => count($groupPermissions)]) }}
                                        </div>
                                        </div>
                                        @if ($permissionsEditable)
                                            <div class="flex gap-2">
                                                <x-lazy-btn ghost sm type="button" @click="toggleGroup($el.closest('fieldset'), true)" :label="__('lazy-admin::access.select_visible')" />
                                                <x-lazy-btn ghost sm type="button" @click="toggleGroup($el.closest('fieldset'), false)" :label="__('lazy-admin::access.clear_visible')" />
                                            </div>
                                        @endif
                                    </div>

                                    <div class="grid gap-2 xl:grid-cols-2">
                                        @foreach ($groupPermissions as $permissionItem)
                                            <label
                                                data-permission
                                                data-search="{{ $group.' '.$permissionItem['permission']->name }}"
                                                x-show="matches($el.dataset.search)"
                                                class="flex min-w-0 items-start gap-3 rounded-lg border border-base-300 p-3 {{ $permissionsEditable ? 'cursor-pointer hover:bg-base-200' : 'opacity-70' }}"
                                            >
                                                <x-lazy-checkbox
                                                    primary
                                                    sm
                                                    name="permissions[]"
                                                    :value="$permissionItem['permission']->name"
                                                    :checked="$permissionItem['checked']"
                                                    :disabled="! $permissionsEditable"
                                                />

                                                <span class="min-w-0">
                                                    <span class="block break-words text-sm font-medium">
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
                                    {{ __('lazy-admin::access.no_permissions') }}
                                </div>
                            @endforelse
                        </div>
                        @if ($permissionsEditable)
                            <div class="sticky bottom-3 z-20 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-base-300 bg-base-100 p-4 shadow-lg">
                                <p class="text-sm">
                                    {{ __('lazy-admin::access.selected') }}:
                                    <strong x-text="selected">{{ collect($permissionGroups)->flatten(1)->where('checked', true)->count() }}</strong>
                                    / {{ $permissions->count() }}
                                </p>
                                <x-lazy-btn primary type="submit" :label="__('lazy-admin::access.save')" />
                            </div>
                        @endif
                    </form>

                    @if ($canDeleteRoles && ! $selectedIsSuperAdmin)
                        <form
                            id="delete-selected-role"
                            method="POST"
                            action="{{ route($routePrefix.'.role.destroy', $selectedRole) }}"
                            onsubmit="return confirm('{{ __('lazy-admin::access.confirm_role_delete') }}')"
                        >
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                @endif

                @if ($canViewPermissions)
                    <x-lazy-collapse
                        class="rounded-2xl border border-base-300 bg-base-100 shadow-sm"
                        summary-class="text-lg font-semibold"
                        content-class="space-y-5"
                    >
                        <x-slot:summary>
                            {{ __('lazy-admin::access.catalog') }}
                            <span class="ml-2 text-sm font-normal opacity-50">
                                {{ __('lazy-admin::access.total', ['count' => $permissions->count()]) }}
                            </span>
                        </x-slot:summary>
                            <p class="text-sm opacity-60">
                                {{ __('lazy-admin::access.catalog_description') }}
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
                                        ＋ {{ __('lazy-admin::access.create_permission') }}
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

                                                <x-lazy-btn sm type="submit" :label="__('lazy-admin::access.save')" />
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
                                                    title="{{ __('lazy-admin::access.delete') }}"
                                                >×</x-lazy-btn>
                                            @endif
                                        </form>

                                        @if ($canDeletePermissions)
                                            <form
                                                id="delete-permission-{{ $permission->getKey() }}"
                                                method="POST"
                                                action="{{ route($routePrefix.'.permission.destroy', $permission) }}"
                                                onsubmit="return confirm('{{ __('lazy-admin::access.confirm_permission_delete') }}')"
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
