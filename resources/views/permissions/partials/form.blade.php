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
            value="{{ old('name', $permission?->name) }}"
            class="input input-bordered @error('name') input-error @enderror"
            required
        />
        @error('name')
            <span class="mt-1 text-sm text-error">{{ $message }}</span>
        @enderror
    </label>

    <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>
