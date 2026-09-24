<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <label class="form-control">
        <span class="label-text">{{ __('Name') }}</span>
        <input
            type="text"
            name="name"
            value="{{ old('name', $role?->name) }}"
            class="input input-bordered @error('name') input-error @enderror"
            required
        />
        @error('name')
            <span class="mt-1 text-sm text-error">{{ $message }}</span>
        @enderror
    </label>

    <fieldset>
        <legend class="mb-3 font-medium">{{ __('Permissions') }}</legend>

        @php
            $selectedPermissions = old(
                'permissions',
                $role ? $role->permissions()->pluck('name')->all() : []
            );
        @endphp

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($permissions as $permission)
                <label class="flex items-center gap-2 rounded-box border border-base-300 p-3">
                    <input
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission->name }}"
                        class="checkbox checkbox-primary"
                        @checked(in_array($permission->name, $selectedPermissions, true))
                    />
                    <span>{{ $permission->name }}</span>
                </label>
            @empty
                <p class="text-sm opacity-70">{{ __('No permissions found.') }}</p>
            @endforelse
        </div>

        @error('permissions.*')
            <span class="mt-1 text-sm text-error">{{ $message }}</span>
        @enderror
    </fieldset>

    <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>
