<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <label class="form-control">
        <span class="label-text">{{ __('Name') }}</span>
        <x-lazy-input
            type="text"
            name="name"
            :value="old('name', $user->name)"
            :color="$errors->has('name') ? 'error' : ''"
            required
        />
        @error('name')
            <span class="mt-1 text-sm text-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="form-control">
        <span class="label-text">{{ __('Email') }}</span>
        <x-lazy-input
            type="email"
            name="email"
            :value="old('email', $user->email)"
            :color="$errors->has('email') ? 'error' : ''"
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
        <x-lazy-input
            type="password"
            name="password"
            :color="$errors->has('password') ? 'error' : ''"
            :required="! $user->exists"
            autocomplete="new-password"
        />
        @error('password')
            <span class="mt-1 text-sm text-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="form-control">
        <span class="label-text">{{ __('Confirm password') }}</span>
        <x-lazy-input
            type="password"
            name="password_confirmation"
            :required="! $user->exists"
            autocomplete="new-password"
        />
    </label>

    @if ($roles !== [])
        <fieldset>
            <legend class="mb-2 font-medium">{{ __('Roles') }}</legend>

            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2">
                        <x-lazy-checkbox
                            name="roles[]"
                            :value="$role"
                            :checked="in_array($role, old('roles', $selectedRoles), true)"
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
        <x-lazy-btn primary type="submit" :label="$submitLabel" />
    </div>
</form>
