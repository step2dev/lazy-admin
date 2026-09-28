<x-lazy-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ __('Edit user') }}</h1>
            <x-lazy-btn
                ghost
                :href="route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.index')"
                :label="__('Back')"
            />
        </div>

        @if (session('status'))
            <x-lazy-alert success class="mb-6" :message="session('status')" />
        @endif

        @include('lazy::users.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.update', $user->getKey()),
            'method' => 'PUT',
            'submitLabel' => __('Save changes'),
        ])

        <x-lazy-divider class="my-8" />

        <div class="rounded-box border border-error/30 p-6">
            <h2 class="text-lg font-semibold text-error">{{ __('Delete user') }}</h2>
            <p class="mt-2 text-sm opacity-70">{{ __('This action cannot be undone.') }}</p>

            <form method="POST"
                  action="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.destroy', $user->getKey()) }}"
                  class="mt-4"
                  onsubmit="return confirm('{{ __('Delete this user?') }}')">
                @csrf
                @method('DELETE')

                <x-lazy-btn error type="submit" :label="__('Delete user')" />
            </form>
        </div>
    </div>
</x-lazy-layout>
