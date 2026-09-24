<x-lazy-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ __('Create permission') }}</h1>
            <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.permission.index') }}">{{ __('Back') }}</a>
        </div>
        @include('lazy::permissions.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.permission.store'),
            'method' => 'POST',
            'submitLabel' => __('Create permission'),
        ])
    </div>
</x-lazy-layout>
