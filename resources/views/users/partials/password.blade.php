<section class="rounded-2xl border border-base-300 bg-base-100 p-6">
    <h2 class="text-lg font-semibold">{{ __('lazy-admin::users.password') }}</h2>
    <p class="mt-1 text-sm text-base-content/60">{{ __('lazy-admin::users.password_help') }}</p>
    <form method="POST" action="{{ $passwordAction }}" class="mt-6 space-y-4">
        @csrf
        @method('PUT')
        @foreach (['current_password' => 'current_password', 'password' => 'new_password', 'password_confirmation' => 'confirm_password'] as $field => $label)
            <label class="flex flex-col gap-2">
                <span class="text-sm font-medium">{{ __('lazy-admin::users.'.$label) }}</span>
                <x-lazy-input type="password" :name="$field" :autocomplete="$field === 'current_password' ? 'current-password' : 'new-password'" required />
                @if ($errors->password->has($field))
                    <span class="text-sm text-error">{{ $errors->password->first($field) }}</span>
                @endif
            </label>
        @endforeach
        <div class="flex justify-end"><x-lazy-btn primary type="submit" :label="__('lazy-admin::users.change_password')" /></div>
    </form>
</section>
