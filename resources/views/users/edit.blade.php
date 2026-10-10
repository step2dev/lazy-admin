<x-lazy-layout :title="__('lazy-admin::users.account')" :routes="['create' => null, 'show' => null, 'edit' => null, 'destroy' => null]">
    @php($prefix = trim(config('lazy.admin.route.name', 'admin.'), '.'))
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-base-content/60">{{ __('lazy-admin::users.users') }}</p>
                <h1 class="mt-1 text-2xl font-semibold">{{ $user->name }}</h1>
                <p class="mt-1 text-sm text-base-content/60">{{ $user->email }}</p>
            </div>
            <x-lazy-btn ghost :href="route($prefix.'.user.index')" :label="__('lazy-admin::users.back')" />
        </div>
        @if (session('status'))
            <x-lazy-alert success :message="session('status')" />
        @endif
        <div class="grid items-start gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-base-300 bg-base-100 p-6">
                <h2 class="mb-6 text-lg font-semibold">{{ __('lazy-admin::users.account') }}</h2>
                @include('lazy::users.partials.form', [
                    'action' => route($prefix.'.user.update', $user->getKey()),
                    'method' => 'PUT',
                    'submitLabel' => __('lazy-admin::users.save'),
                ])
            </section>
            <div class="space-y-6">
                @include('lazy::users.partials.password', ['passwordAction' => route($prefix.'.user.password', $user->getKey())])
                <section class="rounded-2xl border border-base-300 bg-base-100 p-6">
                    <h2 class="text-lg font-semibold">{{ __('lazy-admin::users.security') }}</h2>
                    <p class="mt-3 text-sm text-base-content/60">{{ __('lazy-admin::users.two_factor') }}</p>
                    <p class="mt-1 font-medium">{{ $user->two_factor_secret && $user->two_factor_confirmed_at ? __('lazy-admin::users.enabled') : __('lazy-admin::users.disabled') }}</p>
                    <p class="mt-3 text-sm text-base-content/60">{{ __('lazy-admin::users.owner_security') }}</p>
                </section>
            </div>
        </div>
        @if ($canDelete)
            <section class="rounded-2xl border border-error/30 bg-base-100 p-6">
                <h2 class="text-lg font-semibold text-error">{{ __('lazy-admin::users.delete') }}</h2>
                <p class="mt-2 text-sm text-base-content/60">{{ __('lazy-admin::users.delete_help') }}</p>
                <form method="POST" action="{{ route($prefix.'.user.destroy', $user->getKey()) }}" class="mt-4" onsubmit="return confirm(@js(__('lazy-admin::users.delete_confirm')))">
                    @csrf
                    @method('DELETE')
                    <x-lazy-btn error type="submit" :label="__('lazy-admin::users.delete')" />
                </form>
            </section>
        @endif
    </div>
</x-lazy-layout>
