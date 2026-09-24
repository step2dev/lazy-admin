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
            value="{{ old('name', $user->name) }}"
            class="input input-bordered @error('name') input-error @enderror"
            required
        />
        @error('name')
            <span class="mt-1 text-sm text-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="form-control">
        <span class="label-text">{{ __('Email') }}</span>
        <input
            type="email"
            name="email"
            value="{{ old('email', $user->email) }}"
            class="input input-bordered @error('email') input-error @enderror"
            required
        />
        @error('email')
            <span class="mt-1 text-sm text-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="form-control">
        <span class="label-text">
            {{ $user->exists ? __('New password') : __('Password') }}
        </span>
        <input
            type="password"
            name="password"
            class="input input-bordered @error('password') input-error @enderror"
            @unless($user->exists) required @endunless
            autocomplete="new-password"
        />
        @error('password')
            <span class="mt-1 text-sm text-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="form-control">
        <span class="label-text">{{ __('Confirm password') }}</span>
        <input
            type="password"
            name="password_confirmation"
            class="input input-bordered"
            @unless($user->exists) required @endunless
            autocomplete="new-password"
        />
    </label>

    @if ($roles !== [])
        @php($selectedRoles = old('roles', $user->exists && method_exists($user, 'roles') ? $user->roles()->pluck('name')->all() : []))

        <fieldset>
            <legend class="mb-2 font-medium">{{ __('Roles') }}</legend>

            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role }}"
                            class="checkbox"
                            @checked(in_array($role, $selectedRoles, true))
                        />
                        <span>{{ $role }}</span>
                    </label>
                @endforeach
            </div>

            @error('roles.*')
                <span class="mt-1 text-sm text-error">{{ $message }}</span>
            @enderror
        </fieldset>
    @endif

    <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>
