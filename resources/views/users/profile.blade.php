<x-lazy-layout :title="__('lazy-admin::users.my_profile')">
    @php($prefix = trim(config('lazy.admin.route.name', 'admin.'), '.'))
    <div class="mx-auto max-w-6xl space-y-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ __('lazy-admin::users.my_profile') }}</h1>
            <p class="mt-1 text-base-content/60">{{ __('lazy-admin::users.profile_help') }}</p>
        </div>
        @if (session('status') && ! str_starts_with(session('status'), 'two-factor'))
            <x-lazy-alert success :message="session('status')" />
        @endif
        <div class="grid items-start gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-base-300 bg-base-100 p-6">
                <h2 class="text-lg font-semibold">{{ __('lazy-admin::users.account') }}</h2>
                <form method="POST" action="{{ route($prefix.'.profile.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')
                    @foreach (['name' => 'text', 'email' => 'email', 'current_password' => 'password'] as $field => $type)
                        <label class="flex flex-col gap-2">
                            <span class="text-sm font-medium">{{ __('lazy-admin::users.'.$field) }}</span>
                            <x-lazy-input :type="$type" :name="$field" :value="$type === 'password' ? '' : old($field, $user->getAttribute($field))" :autocomplete="$type === 'password' ? 'current-password' : $field" required />
                            @if ($errors->profile->has($field))
                                <span class="text-sm text-error">{{ $errors->profile->first($field) }}</span>
                            @endif
                        </label>
                    @endforeach
                    <div class="flex justify-end"><x-lazy-btn primary type="submit" :label="__('lazy-admin::users.save')" /></div>
                </form>
            </section>
            <div class="space-y-6">
                @include('lazy::users.partials.password', ['passwordAction' => route($prefix.'.profile.password')])
                @if ($twoFactorAvailable)
                    @include('lazy::users.partials.two-factor')
                @endif
            </div>
        </div>
    </div>
</x-lazy-layout>
