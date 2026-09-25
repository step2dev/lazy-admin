<x-lazy-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ __('Edit user') }}</h1>
            <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.index') }}">
                {{ __('Back') }}
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif

        @include('lazy::users.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.update', $user->getKey()),
            'method' => 'PUT',
            'submitLabel' => __('Save changes'),
        ])

        <div class="divider my-8"></div>

        <div class="rounded-box border border-error/30 p-6">
            <h2 class="text-lg font-semibold text-error">{{ __('Delete user') }}</h2>
            <p class="mt-2 text-sm opacity-70">{{ __('This action cannot be undone.') }}</p>

            <form method="POST"
                  action="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.destroy', $user->getKey()) }}"
                  class="mt-4"
                  onsubmit="return confirm('{{ __('Delete this user?') }}')">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-error">{{ __('Delete user') }}</button>
            </form>
        </div>
    </div>
</x-lazy-layout>
