<x-lazy-layout>
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ __('Permissions') }}</h1>
                <p class="mt-1 text-sm opacity-70">{{ __('Manage permissions used by Lazy Admin roles and modules.') }}</p>
            </div>

            @can('permission_create')
                <a class="btn btn-primary" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.permission.create') }}">
                    {{ __('Create permission') }}
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
                        <th>{{ __('Permission') }}</th>
                        <th>{{ __('Roles') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissions as $permission)
                        <tr>
                            <td class="font-medium">{{ $permission->name }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($permission->roles->pluck('name') as $role)
                                        <span class="badge badge-outline">{{ $role }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    @can('permission_edit')
                                        <a class="btn btn-sm" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.permission.edit', $permission) }}">
                                            {{ __('Edit') }}
                                        </a>
                                    @endcan

                                    @can('permission_delete')
                                        <form method="POST"
                                              action="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.permission.destroy', $permission) }}"
                                              onsubmit="return confirm('{{ __('Delete this permission?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-error" type="submit">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center opacity-70">{{ __('No permissions found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-lazy-layout>
