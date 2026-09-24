<x-lazy-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ __('Create user') }}</h1>
            <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.index') }}">
                {{ __('Back') }}
            </a>
        </div>

        @include('lazy::users.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.store'),
            'method' => 'POST',
            'submitLabel' => __('Create user'),
        ])
    </div>
</x-lazy-layout>
